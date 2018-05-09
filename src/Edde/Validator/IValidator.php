<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	interface IValidator {
		/**
		 * validate the given input
		 *
		 * @param mixed $value
		 */
		public function validate($value): void;
	}
