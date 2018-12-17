<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use function json_decode;
	use function json_encode;

	class JsonFilter extends AbstractFilter {
		/** @inheritdoc */
		public function input($value, ?array $options = null) {
			return json_decode($value);
		}

		/** @inheritdoc */
		public function output($value, ?array $options = null) {
			return json_encode($value);
		}
	}
