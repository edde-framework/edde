<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Schema\IProperty;
	use Edde\Service\Schema\SchemaManager;

	class SchemaValidator extends AbstractValidator {
		use SchemaManager;

		/** @inheritdoc */
		public function validate($value, array $options = []): void {
			/** @var $property IProperty */
			$property = $options['property'];
			$this->schemaManager->validate($this->schemaManager->load($property->getType()), $value);
		}
	}