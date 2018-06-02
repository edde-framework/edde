<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\Entity;
	use Edde\Collection\IEntity;
	use Edde\Config\ISection;
	use Edde\Filter\FilterException;
	use Edde\Query\IQuery;
	use Edde\Schema\IAttribute;
	use Edde\Schema\ISchema;
	use Edde\Service\Config\ConfigService;
	use Edde\Service\Filter\FilterManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Utils\StringUtils;
	use Edde\Service\Validator\ValidatorManager;
	use Edde\Validator\ValidatorException;
	use stdClass;
	use function sprintf;

	abstract class AbstractStorage extends AbstractTransaction implements IStorage {
		use ConfigService;
		use SchemaManager;
		use StringUtils;
		use FilterManager;
		use ValidatorManager;
		/** @var string */
		protected $config;
		/** @var ISection */
		protected $section;
		/** @var ICompiler */
		protected $compiler;

		/**
		 * @param string $config
		 */
		public function __construct(string $config) {
			$this->config = $config;
		}

		/** @inheritdoc */
		public function saves(iterable $entities): IStorage {
			$this->transaction(function () use ($entities) {
				foreach ($entities as $entity) {
					$this->save($entity);
				}
			});
			return $this;
		}

		/** @inheritdoc */
		public function attach(IEntity $source, IEntity $target, string $relation): IEntity {
			$relationEntity = new Entity($relationSchema = $this->schemaManager->getSchema($relation));
			$relationSchema->checkRelation(
				$source->getSchema(),
				$target->getSchema()
			);
			$this->save($source);
			$this->save($target);
			$relationEntity->set(
				$relationSchema->getSource()->getName(),
				$source->getPrimary()->get()
			);
			$relationEntity->set(
				$relationSchema->getTarget()->getName(),
				$target->getPrimary()->get()
			);
			return $relationEntity;
		}

		/** @inheritdoc */
		public function link(IEntity $source, IEntity $target, string $relation): IEntity {
			$this->unlink($source, $target, $relation);
			return $this->attach($source, $target, $relation);
		}

		/** @inheritdoc */
		public function count(IQuery $query): array {
			try {
				foreach ($this->fetch($this->compiler->compile($query->count(true))) as $row) {
					return $row;
				}
				throw new StorageException(sprintf('Cannot get counts from a query.'));
			} finally {
				$query->count(false);
			}
		}

		/**
		 * sanitizer and validate input
		 *
		 * @param IEntity $entity
		 *
		 * @return stdClass
		 *
		 * @throws FilterException
		 * @throws ValidatorException
		 */
		protected function prepareInsert(IEntity $entity): stdClass {
			$stdClass = $entity->toObject();
			$schema = $entity->getSchema();
			foreach ($schema->getAttributes() as $name => $attribute) {
				if (($generator = $attribute->getFilter('generator')) && $stdClass->$name === null) {
					$stdClass->$name = $this->filterManager->getFilter('storage:' . $generator)->input(null);
				}
				$stdClass->$name = $stdClass->$name ?: $attribute->getDefault();
				if ($validator = $attribute->getValidator()) {
					$this->validatorManager->validate('storage:' . $validator, $stdClass->$name, (object)[
						'name'     => $schema->getName() . '::' . $name,
						'required' => $attribute->isRequired(),
					]);
				}
				$stdClass->$name = $this->filterValue($attribute, $stdClass->$name);
			}
			return $stdClass;
		}

		/**
		 * @param IAttribute $attribute
		 * @param mixed      $value
		 *
		 * @return mixed
		 *
		 * @throws FilterException
		 */
		protected function filterValue(IAttribute $attribute, $value) {
			if ($filter = $attribute->getFilter('type')) {
				$value = $this->filterManager->getFilter('storage:' . $filter)->input($value);
			}
			/**
			 * common filter support; filter name is used for both directions
			 */
			if ($filter = $attribute->getFilter('filter')) {
				$value = $this->filterManager->getFilter('storage:' . $filter)->input($value);
			}
			return $value;
		}

		/**
		 * @param IEntity $entity
		 *
		 * @return stdClass
		 *
		 * @throws FilterException
		 * @throws ValidatorException
		 */
		protected function prepareUpdate(IEntity $entity): stdClass {
			$stdClass = $entity->toObject();
			$schema = $entity->getSchema();
			foreach ($schema->getAttributes() as $name => $attribute) {
				if ($validator = $attribute->getValidator()) {
					$this->validatorManager->validate('storage:' . $validator, $stdClass->$name, (object)[
						'name'     => $schema->getName() . '::' . $name,
						'required' => $attribute->isRequired(),
					]);
				}
				if ($filter = $attribute->getFilter('type')) {
					$stdClass->$name = $this->filterManager->getFilter('storage:' . $filter)->input($stdClass->$name);
				}
				/**
				 * common filter support; filter name is used for both directions
				 */
				if ($filter = $attribute->getFilter('filter')) {
					$stdClass->$name = $this->filterManager->getFilter('storage:' . $filter)->input($stdClass->$name);
				}
			}
			return $stdClass;
		}

		/**
		 * validate and sanitize output
		 *
		 * @param ISchema  $schema
		 * @param stdClass $stdClass
		 *
		 * @return stdClass
		 * @throws FilterException
		 */
		protected function prepareOutput(ISchema $schema, stdClass $stdClass): stdClass {
			$stdClass = clone $stdClass;
			foreach ($schema->getAttributes() as $name => $attribute) {
				if (property_exists($stdClass, $name) === false) {
					$stdClass->$name = $attribute->getDefault();
				}
				if ($filter = $attribute->getFilter('type')) {
					$stdClass->$name = $this->filterManager->getFilter('storage:' . $filter)->output($stdClass->$name);
				}
				if ($filter = $attribute->getFilter('filter')) {
					$stdClass->$name = $this->filterManager->getFilter('storage:' . $filter)->output($stdClass->$name);
				}
			}
			return $stdClass;
		}

		/**
		 * @param array     $row
		 * @param ISchema[] $schemas
		 * @param string[]  $selects
		 *
		 * @return IRow
		 * @throws FilterException
		 */
		protected function row(array $row, array $schemas, array $selects): IRow {
			$items = [];
			foreach ($row as $k => $v) {
				[$alias, $property] = explode('.', $k, 2);
				$items[$alias] = $items[$alias] ?? new stdClass();
				$items[$alias]->$property = $v;
			}
			foreach ($items as $alias => $item) {
				$items[$alias] = $this->prepareOutput($schemas[$selects[$alias]], $item);
			}
			return new Row($items);
		}

		/** @inheritdoc */
		protected function handleSetup(): void {
			parent::handleSetup();
			$this->section = $this->configService->require($this->config);
			$this->compiler = $this->createCompiler();
		}
	}
