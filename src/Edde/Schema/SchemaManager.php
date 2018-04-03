<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Obj3ct;
	use Edde\Service\Filter\FilterManager;
	use Edde\Service\Generator\GeneratorManager;
	use Edde\Service\Sanitizer\SanitizerManager;
	use Edde\Service\Schema\SchemaLoader;
	use Edde\Service\Validator\ValidatorManager;
	use Edde\Validator\ValidationException;
	use Edde\Validator\ValidatorException;
	use stdClass;

	class SchemaManager extends Obj3ct implements ISchemaManager {
		use SchemaLoader;
		use GeneratorManager;
		use FilterManager;
		use SanitizerManager;
		use ValidatorManager;
		/** @var ISchema[] */
		protected $schemas = [];

		/** @inheritdoc */
		public function hasSchema(string $schema): bool {
			try {
				$this->load($schema);
				return true;
			} catch (SchemaException $exception) {
				return false;
			}
		}

		/** @inheritdoc */
		public function load(string $name): ISchema {
			if (isset($this->schemas[$name])) {
				return $this->schemas[$name];
			}
			$this->schemas[$name] = $schema = $this->schemaLoader->load($name);
			if ($schema->hasAlias()) {
				$this->schemas[$schema->getAlias()] = $schema;
			}
			return $schema;
		}

		/** @inheritdoc */
		public function generate(ISchema $schema, stdClass $source): stdClass {
			$result = clone $source;
			foreach ($schema->getAttributes() as $property) {
				if (isset($source->{$name = $property->getName()}) === false && ($generator = $property->getGenerator())) {
					$result->$name = $this->generatorManager->getGenerator($generator)->generate();
				}
			}
			return $result;
		}

		/** @inheritdoc */
		public function filter(ISchema $schema, stdClass $source): stdClass {
			$result = clone $source;
			foreach ($source as $k => $v) {
				if ($filter = $schema->getAttribute($k)->getFilter()) {
					$result->$k = $this->filterManager->getFilter($filter)->filter($v);
				}
			}
			return $result;
		}

		/** @inheritdoc */
		public function sanitize(ISchema $schema, stdClass $source): stdClass {
			$result = $source;
			foreach ($source as $k => $v) {
				if ($sanitizer = $schema->getAttribute($k)->getSanitizer()) {
					$result->$k = $this->sanitizerManager->getSanitizer($sanitizer)->sanitize($v);
				}
			}
			return $result;
		}

		/** @inheritdoc */
		public function isValid(ISchema $schema, stdClass $source): bool {
			try {
				$this->validate($schema, $source);
				return true;
			} catch (ValidatorException | SchemaValidationException $exception) {
				return false;
			}
		}

		/** @inheritdoc */
		public function validate(ISchema $schema, stdClass $source): void {
			$exceptions = [];
			foreach ($schema->getAttributes() as $name => $property) {
				try {
					if ($property->isLink()) {
						continue;
					}
					$options = [
						'::name'   => $schema->getName() . '::' . $name,
						'schema'   => $schema,
						'property' => $property,
					];
					$value = $source->$name ?? null;
					if ($property->isRequired()) {
						$this->validatorManager->check('required', $value, $options);
					}
					if ($value && $this->validatorManager->hasValidator($validator = ('type:' . $property->getType()))) {
						$this->validatorManager->check($validator, $value, $options);
					}
					if ($validator = $property->getValidator()) {
						$this->validatorManager->getValidator($validator)->validate($value, $options);
					}
				} catch (SchemaValidationException $exception) {
					$exceptions[$name] = [
						'value'       => $value,
						'message'     => $exception->getMessage(),
						'validations' => $exception->getValidations(),
					];
				} catch (ValidationException $exception) {
					$exceptions[$name] = [
						'value'   => $value,
						'message' => $exception->getMessage(),
					];
				}
			}
			if (empty($exceptions) === false) {
				throw new SchemaValidationException(
					sprintf('Validation of schema [%s] failed.', $schema->getName()),
					$exceptions
				);
			}
		}

		/** @inheritdoc */
		public function check(string $schema, stdClass $source): void {
			$this->validate($this->load($schema), $source);
		}
	}
