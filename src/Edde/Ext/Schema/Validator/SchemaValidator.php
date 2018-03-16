<?php
	declare(strict_types=1);
	namespace Edde\Ext\Schema\Validator;

	use Edde\Api\Schema\IProperty;
	use Edde\Api\Validator\Exception\UnknownValidatorException;
	use Edde\Api\Validator\Exception\ValidationException;
	use Edde\Common\Validator\AbstractValidator;
	use Edde\Exception\Schema\UnknownPropertyException;
	use Edde\Exception\Schema\UnknownSchemaException;
	use Edde\Inject\Schema\SchemaManager;

	class SchemaValidator extends AbstractValidator {
		use SchemaManager;

		/**
		 * @inheritdoc
		 *
		 * @throws UnknownValidatorException
		 * @throws ValidationException
		 * @throws UnknownPropertyException
		 * @throws UnknownSchemaException
		 */
		public function validate($value, array $options = []): void {
			/** @var $property IProperty */
			$property = $options['property'];
			$this->schemaManager->validate($this->schemaManager->load($property->getType()), $value);
		}
	}
