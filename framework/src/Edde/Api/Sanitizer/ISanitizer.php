<?php
	declare(strict_types=1);
	namespace Edde\Api\Sanitizer;

	/**
	 * Sanitizer should prepare a value to be exported out of PHP (for example converting
	 * date times to string, ...).
	 */
	interface ISanitizer {
		/**
		 * do the magic around the given value
		 *
		 * @param mixed $value
		 * @param array $options
		 *
		 * @return mixed
		 */
		public function sanitize($value, array $options = []);
	}
