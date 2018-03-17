<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	class SchemaValidationException extends SchemaException {
		protected $validations;

		public function __construct(string $message = "", array $validations = []) {
			parent::__construct($message);
			$this->validations = $validations;
		}

		public function getValidations(): array {
			return $this->validations;
		}
	}
