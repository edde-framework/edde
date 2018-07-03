<?php
	declare(strict_types = 1);

	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaCollection;
	use Edde\Api\Schema\ISchemaLink;
	use Edde\Api\Schema\ISchemaProperty;
	use Edde\Api\Schema\SchemaException;
	use Edde\Common\Deffered\AbstractDeffered;

	class Schema extends AbstractDeffered implements ISchema {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var string
		 */
		protected $namespace;
		/**
		 * @var string
		 */
		protected $schemaName;
		/**
		 * @var ISchemaProperty[]
		 */
		protected $propertyList = [];
		/**
		 * @var ISchemaLink[]
		 */
		protected $linkList = [];
		/**
		 * @var ISchemaCollection[]
		 */
		protected $collectionList = [];
		/**
		 * meta-data
		 *
		 * @var array
		 */
		protected $metaList = [];

		/**
		 * @param string $name
		 * @param string $namespace
		 */
		public function __construct(string $name, string $namespace = null) {
			$this->name = $name;
			$this->namespace = $namespace;
			if ($namespace === null) {
				$nameList = explode('\\', $name);
				$this->name = end($nameList);
				array_pop($nameList);
				if (empty($nameList) === false) {
					$this->namespace = implode('\\', $nameList);
				}
			}
		}

		public function getName(): string {
			return $this->name;
		}

		public function getNamespace(): string {
			return $this->namespace;
		}

		public function getProperty(string $name): ISchemaProperty {
			$this->use();
			if ($this->hasProperty($name) === false) {
				throw new SchemaException(sprintf('Requested unknown property [%s] in schema [%s].', $name, $this->getSchemaName()));
			}
			return $this->propertyList[$name];
		}

		public function hasProperty(string $name): bool {
			$this->use();
			return isset($this->propertyList[$name]);
		}

		public function getSchemaName(): string {
			if ($this->schemaName === null) {
				$this->schemaName = (($namespace = $this->namespace) !== null ? $namespace . '\\' : '') . $this->name;
			}
			return $this->schemaName;
		}

		public function getPropertyList(): array {
			$this->use();
			return $this->propertyList;
		}

		public function addPropertyList(array $schemaPropertyList): ISchema {
			foreach ($schemaPropertyList as $schemaProperty) {
				$this->addProperty($schemaProperty);
			}
			return $this;
		}

		public function addProperty(ISchemaProperty $schemaProperty, bool $force = false): ISchema {
			$propertyName = $schemaProperty->getName();
			if ($schemaProperty->getSchema() !== $this) {
				throw new SchemaException(sprintf('Cannot add foreign property [%s] to schema [%s].', $propertyName, $this->getSchemaName()));
			}
			if ($force === false && isset($this->propertyList[$propertyName])) {
				throw new SchemaException(sprintf('Property with name [%s] already exists in schema [%s].', $propertyName, $this->getSchemaName()));
			}
			$this->propertyList[$propertyName] = $schemaProperty;
			return $this;
		}

		public function hasLink(string $name): bool {
			return isset($this->linkList[$name]);
		}

		public function getLink(string $name): ISchemaLink {
			if (isset($this->linkList[$name]) === false) {
				throw new SchemaException(sprintf('Requested unknown link [%s] in schema [%s].', $name, $this->getSchemaName()));
			}
			return $this->linkList[$name];
		}

		public function getLinkList(): array {
			return $this->linkList;
		}

		public function collection(string $name, ISchemaProperty $source, ISchemaProperty $target, bool $force = false): ISchema {
			if (isset($this->collectionList[$name]) && $force === false) {
				throw new SchemaException(sprintf('Schema [%s] already has collection named [%s].', $this->getSchemaName(), $name));
			}
			$this->collectionList[$name] = new SchemaCollection($name, $source, $target);
			return $this;
		}

		public function hasCollection(string $name): bool {
			return isset($this->collectionList[$name]);
		}

		public function getCollection(string $name): ISchemaCollection {
			if (isset($this->collectionList[$name]) === false) {
				throw new SchemaException(sprintf('Requested unknown collection [%s] in schema [%s].', $name, $this->getSchemaName()));
			}
			return $this->collectionList[$name];
		}

		public function getCollectionList(): array {
			return $this->collectionList;
		}

		public function linkTo(string $link, string $collection, ISchemaProperty $source, ISchemaProperty $target): ISchema {
			if ($source->getSchema() !== $this) {
				throw new SchemaException(sprintf('Source property [%s] is not part of the current schema [%s].', $source->getPropertyName(), $this->getSchemaName()));
			}
			$this->link($link, $source, $target);
			$target->getSchema()
				->collection($collection, $target, $source);
			return $this;
		}

		public function link(string $name, ISchemaProperty $source, ISchemaProperty $target, bool $force = false): ISchema {
			if (isset($this->linkList[$name]) && $force === false) {
				throw new SchemaException(sprintf('Schema [%s] already contains link named [%s].', $this->getSchemaName(), $name));
			}
			$this->linkList[$name] = new SchemaLink($name, $source, $target);
			return $this;
		}

		public function getMeta(string $name, $default = null) {
			$this->use();
			return $this->metaList[$name] ?? $default;
		}

		public function getMetaList(): array {
			$this->use();
			return $this->metaList;
		}

		public function setMetaList(array $metaList) {
			$this->metaList = $metaList;
			return $this;
		}

		public function hasMeta(string $name): bool {
			$this->use();
			return isset($this->metaList[$name]) || array_key_exists($name, $this->metaList);
		}
	}
