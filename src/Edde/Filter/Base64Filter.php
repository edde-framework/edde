<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use function base64_decode;
	use function base64_encode;

	class Base64Filter extends AbstractFilter {
		/** @inheritdoc */
		public function input($value, ?array $options = null) {
			return base64_decode($value);
		}

		/** @inheritdoc */
		public function output($value, ?array $options = null) {
			return base64_encode($value);
		}
	}
