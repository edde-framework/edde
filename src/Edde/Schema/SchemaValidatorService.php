<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Edde;
	use Edde\Service\Validator\ValidatorManager;
	use Edde\Validator\ValidatorException;
	use stdClass;

	class SchemaValidatorService extends Edde implements ISchemaValidatorService {
		use ValidatorManager;

		/** @inheritdoc */
		public function validate(ISchema $schema, stdClass $stdClass, string $context = null): void {
			try {
				foreach ($schema->getAttributes() as $name => $attribute) {
					if ($validator = $attribute->getValidator()) {
						$this->validatorManager->validate(($context ? $context . ':' : '') . $validator, $stdClass->$name ?? null);
					}
				}
			} catch (ValidatorException $exception) {
				throw new SchemaValidationException($exception->getMessage(), 0, $exception);
			}
		}
	}

