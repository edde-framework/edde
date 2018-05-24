<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\Entity;
	use Edde\Collection\IEntity;
	use Edde\Config\ISection;
	use Edde\Filter\FilterException;
	use Edde\Query\IQuery;
	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;
	use Edde\Service\Config\ConfigService;
	use Edde\Service\Filter\FilterManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Utils\StringUtils;
	use Edde\Service\Validator\ValidatorManager;
	use Edde\Validator\ValidatorException;
	use stdClass;

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

		/**
		 * @param string $config
		 */
		public function __construct(string $config) {
			$this->config = $config;
		}

		/** @inheritdoc */
		public function execute(IQuery $query) {
			return $this->{'execute' . $this->stringUtils->toCamelCase($query->getType())}($query);
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
		public function attach(IEntity $entity, IEntity $target, string $relation): IEntity {
			$relationEntity = new Entity($relationSchema = $this->schemaManager->getSchema($relation));
			$entitySchema = $entity->getSchema();
			$targetSchema = $target->getSchema();
			$sourceAttribute = $relationSchema->getSource();
			$targetAttribute = $relationSchema->getTarget();
			$this->checkRelation($relationSchema, $entitySchema, $targetSchema);
			$this->save($entity);
			$this->save($target);
			$relationEntity->set($sourceAttribute->getName(), $entity->getPrimary()->get());
			$relationEntity->set($targetAttribute->getName(), $target->getPrimary()->get());
			return $relationEntity;
		}

		/** @inheritdoc */
		public function link(IEntity $entity, IEntity $target, string $relation): IEntity {
			$this->unlink($entity, $target, $relation);
			return $this->attach($entity, $target, $relation);
		}

		/**
		 * @param ISchema $relationSchema
		 * @param ISchema $entitySchema
		 * @param ISchema $targetSchema
		 *
		 * @throws StorageException
		 * @throws SchemaException
		 */
		protected function checkRelation(ISchema $relationSchema, ISchema $entitySchema, ISchema $targetSchema): void {
			$sourceAttribute = $relationSchema->getSource();
			$targetAttribute = $relationSchema->getTarget();
			if ($relationSchema->isRelation() === false) {
				throw new StorageException(sprintf('Cannot attach [%s] to [%s] because relation [%s] is not relation.', $entitySchema->getName(), $targetSchema->getName(), $relationSchema->getName()));
			} else if (($expectedSchemaName = $sourceAttribute->getSchema()) !== ($schemaName = $entitySchema->getName())) {
				throw new StorageException(sprintf('Source schema [%s] of entity differs from expected relation [%s] source schema [%s]; did you swap source ($entity) and $target?.', $schemaName, $relationSchema->getName(), $expectedSchemaName));
			} else if (($expectedSchemaName = $targetAttribute->getSchema()) !== ($schemaName = $targetSchema->getName())) {
				throw new StorageException(sprintf('Target schema [%s] of entity differs from expected relation [%s] source schema [%s]; did you swap source ($entity) and $target?.', $schemaName, $relationSchema->getName(), $expectedSchemaName));
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
		 * @param string[]  $uses
		 *
		 * @return IRow
		 * @throws FilterException
		 */
		protected function row(array $row, array $schemas, array $uses): IRow {
			$items = [];
			foreach ($row as $k => $v) {
				[$alias, $property] = explode('.', $k, 2);
				$items[$alias] = $items[$alias] ?? new stdClass();
				$items[$alias]->$property = $v;
			}
			foreach ($items as $alias => $item) {
				$items[$alias] = $this->prepareOutput($schemas[$uses[$alias]], $item);
			}
			return new Row($items);
		}

		/** @inheritdoc */
		protected function handleSetup(): void {
			parent::handleSetup();
			$this->section = $this->configService->require($this->config);
		}
	}
