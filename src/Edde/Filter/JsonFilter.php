<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use function json_decode;

	class JsonFilter extends AbstractFilter {
		/** @inheritdoc */
		public function filter($value, array $options = []) {
			return json_decode($value);
		}
	}
