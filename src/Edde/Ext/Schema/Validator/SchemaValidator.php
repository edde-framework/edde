<?php
	declare(strict_types=1);
	namespace Edde\Ext\Schema\Validator;

	use Edde\Api\Schema\Inject\SchemaManager;
	use Edde\Api\Schema\IProperty;
	use Edde\Common\Validator\AbstractValidator;

	class SchemaValidator extends AbstractValidator {
		use SchemaManager;

		/** @inheritdoc */
		public function validate($value, array $options = []): void {
			/** @var $property IProperty */
			$property = $options['property'];
			$this->schemaManager->validate($this->schemaManager->load($property->getType()), $value);
		}
	}
