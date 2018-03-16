<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Inject\Schema\SchemaManager;
	use Edde\Schema\IProperty;

	class SchemaValidator extends AbstractValidator {
		use SchemaManager;

		/** @inheritdoc */
		public function validate($value, array $options = []): void {
			/** @var $property IProperty */
			$property = $options['property'];
			$this->schemaManager->validate($this->schemaManager->load($property->getType()), $value);
		}
	}
