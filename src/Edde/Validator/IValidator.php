<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use stdClass;

	interface IValidator {
		/**
		 * validate the given input
		 *
		 * @param mixed         $value
		 * @param stdClass|null $options
		 *
		 * @throws ValidatorException
		 */
		public function validate($value, ?stdClass $options = null): void;
	}
