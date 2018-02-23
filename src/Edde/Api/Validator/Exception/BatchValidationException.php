<?php
	declare(strict_types=1);
	namespace Edde\Api\Validator\Exception;

	class BatchValidationException extends ValidationException {
		protected $validations;

		public function __construct(string $message = "", array $validations = []) {
			parent::__construct($message);
			$this->validations = $validations;
		}

		public function getValidations(): array {
			return $this->validations;
		}
	}
