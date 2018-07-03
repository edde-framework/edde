<?php
	declare(strict_types=1);

	namespace Edde\Common\Crate;

	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICollection;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\IProperty;
	use Edde\Api\Crypt\CryptException;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Object;
	use Edde\Common\Reflection\ReflectionUtils;
	use Edde\Common\Schema\Property as SchemaProperty;
	use Edde\Common\Schema\Schema;

	/**
	 * Simple (...advanced...) crate implementation.
	 */
	class Crate extends Object implements ICrate {
		/**
		 * @var ISchema
		 */
		protected $schema;
		/**
		 * @var IProperty[]
		 */
		protected $propertyList = [];
		/**
		 * @var IProperty[]
		 */
		protected $identifierList;
		/**
		 * @var ICollection[]
		 */
		protected $collectionList = [];
		/**
		 * @var ICrate[]|callable[]
		 */
		protected $linkList = [];
		/**
		 * @var callable
		 */
		protected $commit;

		/**
		 * @inheritdoc
		 * @throws CrateException
		 */
		public function getSchema(): ISchema {
			if ($this->schema === null) {
				throw new CrateException(sprintf('Cannot get schema from anonymous crate [%s].', static::class));
			}
			return $this->schema;
		}

		/**
		 * @inheritdoc
		 */
		public function setSchema(ISchema $schema): ICrate {
			$this->schema = $schema;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getPropertyList(): array {
			return $this->propertyList;
		}

		/**
		 * @inheritdoc
		 */
		public function getIdentifierList(): array {
			if ($this->identifierList === null) {
				$this->identifierList = [];
				foreach ($this->propertyList as $property) {
					if ($property->getSchemaProperty()->isIdentifier()) {
						$this->identifierList[] = $property;
					}
				}
			}
			return $this->identifierList;
		}

		/**
		 * @inheritdoc
		 */
		public function set(string $name, $value): ICrate {
			$this->getProperty($name)->set($value);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws CrateException
		 */
		public function getProperty(string $name): IProperty {
			if ($this->hasProperty($name) === false) {
				throw new CrateException(sprintf('Unknown value [%s] in crate [%s].', $name, $this->schema ? $this->schema->getSchemaName() : static::class . '; anonymous'));
			}
			return $this->propertyList[$name];
		}

		/**
		 * @inheritdoc
		 */
		public function hasProperty(string $name): bool {
			return isset($this->propertyList[$name]);
		}

		/**
		 * @inheritdoc
		 * @throws CrateException
		 */
		public function add(string $name, $value, $key = null): ICrate {
			$property = $this->getProperty($name)->getSchemaProperty();
			if ($property->isArray() === false) {
				throw new CrateException(sprintf('Property [%s] is not array; cannot add value.', $property->getPropertyName()));
			}
			$array = $this->get($name);
			if ($key === null) {
				$array[] = $value;
			} else {
				$array[$key] = $value;
			}
			$this->set($name, $array);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws CrateException
		 */
		public function put(array $put, bool $strict = true): ICrate {
			if ($strict && ($diff = array_diff(array_keys($put), array_keys($this->propertyList))) !== []) {
				throw new CrateException(sprintf('Setting unknown values [%s] to the crate [%s].', implode(', ', $diff), $this->schema->getSchemaName()));
			}
			foreach ($put as $property => $value) {
				if (isset($this->propertyList[$property]) === false) {
					continue;
				}
				$this->set($property, $value);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws CrateException
		 */
		public function push(array $push, bool $strict = true): ICrate {
			if ($strict && ($diff = array_diff(array_keys($push), array_keys($this->propertyList))) !== []) {
				throw new CrateException(sprintf('Setting unknown values [%s] to the crate [%s].', implode(', ', $diff), $this->schema->getSchemaName()));
			}
			foreach ($push as $property => $value) {
				$property = $this->getProperty($property = (string)$property);
				$schemaProperty = $property->getSchemaProperty();
				if (($isArray = is_array($value)) === false && $schemaProperty->isArray()) {
					throw new CrateException(sprintf('Cannot push simple value [%s] to array.', $property->getSchemaProperty()));
				}
				if ($isArray && $schemaProperty->isArray() === false) {
					throw new CrateException(sprintf('Cannot push array to simple value [%s].', $property->getSchemaProperty()));
				}
				$property->push($value);
			}
			return $this;
		}

		public function dynamic($source): ICrate {
			$schema = new Schema(static::class);
			foreach ($source as $k => $v) {
				$schema->addProperty($schemaProperty = new SchemaProperty($schema, (string)$k, gettype($v), false));
				$this->addProperty(new Property($schemaProperty, $v));
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws CrateException
		 */
		public function get(string $name, $default = null) {
			return $this->getProperty($name)->get($default);
		}

		/**
		 * @inheritdoc
		 * @throws CrateException
		 */
		public function linkTo(array $linkTo): ICrate {
			foreach ($linkTo as $name => $crate) {
				$this->link($name, $crate);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws CrateException
		 */
		public function link(string $name, ICrate $crate): ICrate {
			if ($this->schema->hasLink($name) === false) {
				throw new CrateException(sprintf('Crate [%s] has no link [%s] in schema [%s].', static::class, $name, $this->schema->getSchemaName()));
			}
			$link = $this->schema->getLink($name);
			$this->linkList[$name] = $crate;
			$this->set($link->getSource()->getName(), $crate->get($link->getTarget()->getName()));
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws CrateException
		 */
		public function proxy(string $name, callable $crate): ICrate {
			if ($this->schema->hasLink($name) === false) {
				throw new CrateException(sprintf('Crate [%s] has no link [%s] in schema [%s].', static::class, $name, $this->schema->getSchemaName()));
			}
			$callback = ReflectionUtils::getMethodReflection($crate);
			if (($returnType = $callback->getReturnType()) === null || (string)$returnType !== ICrate::class) {
				throw new CrateException(sprintf('Proxied callable must have [%s] return typehint in crate [%s].', ICrate::class, static::class));
			}
			$this->linkList[$name] = $crate;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws CrateException
		 */
		public function getLink(string $name) {
			if ($this->hasLink($name) === false) {
				throw new CrateException(sprintf('Requested unknown link [%s] on the crate [%s].', $name, $this->schema->getSchemaName()));
			}
			if (is_callable($this->linkList[$name])) {
				$this->linkList[$name] = $this->linkList[$name]($this, $name);
			}
			return $this->linkList[$name];
		}

		/**
		 * @inheritdoc
		 */
		public function hasLink(string $name): bool {
			return isset($this->linkList[$name]);
		}

		/**
		 * @inheritdoc
		 * @throws CrateException
		 */
		public function collection(string $name, ICollection $collection): ICrate {
			if ($this->schema->hasCollection($name) === false) {
				throw new CrateException(sprintf('Crate [%s] has no collection [%s] in schema [%s].', static::class, $name, $this->schema->getSchemaName()));
			}
			$this->collectionList[$name] = $collection;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws CrateException
		 */
		public function getCollection(string $name): ICollection {
			if ($this->hasCollection($name) === false) {
				throw new CrateException(sprintf('Requested unknown collection [%s] on the crate [%s].', $name, $this->schema->getSchemaName()));
			}
			return $this->collectionList[$name];
		}

		/**
		 * @inheritdoc
		 */
		public function hasCollection(string $name): bool {
			return isset($this->collectionList[$name]);
		}

		/**
		 * @inheritdoc
		 */
		public function getDirtyList(): array {
			if ($this->isDirty() === false) {
				return [];
			}
			$propertyList = [];
			foreach ($this->propertyList as $property) {
				if ($property->isDirty() === false) {
					continue;
				}
				$schemaProperty = $property->getSchemaProperty();
				$propertyList[$schemaProperty->getName()] = $property;
			}
			return $propertyList;
		}

		/**
		 * @inheritdoc
		 */
		public function isDirty(): bool {
			foreach ($this->propertyList as $property) {
				if ($property->isDirty()) {
					return true;
				}
			}
			return false;
		}

		/**
		 * @inheritdoc
		 * @throws CrateException
		 */
		public function addProperty(IProperty $property, bool $force = false): ICrate {
			$schemaProperty = $property->getSchemaProperty();
			if (isset($this->propertyList[$propertyName = $schemaProperty->getName()]) && $force === false) {
				throw new CrateException(sprintf('Property [%s] is already present in crate [%s].', $propertyName, $this->schema->getSchemaName()));
			}
			$this->propertyList[$propertyName] = $property;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function array(): array {
			$array = [];
			foreach ($this->propertyList as $name => $property) {
				$array[$name] = $property->get();
			}
			foreach ($this->collectionList as $name => $collection) {
				/** @var $crate ICrate */
				foreach ($collection as $crate) {
					$array[$name][] = $crate->array();
				}
			}
			foreach ($this->linkList as $name => $crate) {
				$array[$name] = $crate->array();
			}
			return $array;
		}

		/**
		 * @inheritdoc
		 * @throws CryptException
		 */
		public function commit(callable $callback = null): ICrate {
			if ($callback === null && $this->commit === null) {
				throw new CryptException(sprintf('Commit is not available on crate [%s]. It has to be set before calling.', $this->schema->getSchemaName()));
			}
			if ($callback === null) {
				if ($this->isDirty()) {
					/** @noinspection VariableFunctionsUsageInspection */
					call_user_func($this->commit, $this);
				}
				$this->commit = null;
				return $this;
			}
			if ($this->commit !== null) {
				throw new CryptException(sprintf('Commit callback has been already set on crate [%s]; please execute commit before reuse.', $this->schema->getSchemaName()));
			}
			$this->commit = $callback;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function update(): ICrate {
			foreach ($this->propertyList as $property) {
				$schemaProperty = $property->getSchemaProperty();
				if ($property->isEmpty() && $schemaProperty->hasGenerator()) {
					$property->set($schemaProperty->generator());
				}
			}
			return $this;
		}

		public function __clone() {
			parent::__clone();
			foreach ($this->propertyList as &$property) {
				$property = clone $property;
			}
		}
	}
