<?php
	declare(strict_types=1);
	namespace Edde\Exception\Validator;

	class ValidationException extends ValidatorException {
		protected $name;

		public function __construct(string $message = "", string $name = null) {
			parent::__construct($message);
			$this->name = $name;
		}

		public function getName(): ?string {
			return $this->name;
		}
	}
