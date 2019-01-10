<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use function base64_decode;
	use function base64_encode;

	class Base64Filter extends AbstractFilter {
		/** @inheritdoc */
		public function input($value, ?array $options = null) {
			return $value !== null ? base64_decode($value) : null;
		}

		/** @inheritdoc */
		public function output($value, ?array $options = null) {
			return $value !== null ? base64_encode($value) : null;
		}
	}
