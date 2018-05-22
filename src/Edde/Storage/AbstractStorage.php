<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\IEntity;
	use Edde\Config\ISection;
	use Edde\Filter\FilterException;
	use Edde\Query\IQuery;
	use Edde\Schema\ISchema;
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
		 * @param ISchema $schema
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
