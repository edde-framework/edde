<?php
	declare(strict_types=1);
	namespace Edde\Storage;

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
		 * @param ISchema  $schema
		 * @param stdClass $stdClass
		 *
		 * @return stdClass
		 * @throws FilterException
		 * @throws ValidatorException
		 */
		protected function prepareInput(ISchema $schema, stdClass $stdClass): stdClass {
			$stdClass = clone $stdClass;
			foreach ($schema->getAttributes() as $name => $attribute) {
				/**
				 * if there is a generator and property does not exists, generate a new value; property should not exists to
				 * accept NULL and empty values as generated value
				 */
				if (($generator = $attribute->getFilter('generator')) && (property_exists($stdClass, $name) === false)) {
					$stdClass->$name = $this->filterManager->getFilter('storage:' . $generator)->input(null);
				}
				/**
				 * default value will provide default all the times, thus from this point it's safe to use $stdClass->$name
				 */
				if (property_exists($stdClass, $name) === false && ($default = $attribute->getDefault()) !== null) {
					$stdClass->$name = $default;
				}
				if (property_exists($stdClass, $name) === false || $stdClass->$name === null) {
					if ($attribute->isRequired()) {
						throw new ValidatorException(sprintf('Required value [%s::%s] is not set or null.', $schema->getName(), $name));
					}
					continue;
				}
				if ($validator = $attribute->getValidator()) {
					$this->validatorManager->validate('storage:' . $validator, $stdClass->$name, (object)['name' => $schema->getName() . '::' . $name]);
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

		/** @inheritdoc */
		protected function handleSetup(): void {
			parent::handleSetup();
			$this->section = $this->configService->require($this->config);
		}
	}
