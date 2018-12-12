<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\Service\Security\RandomService;

	class UuidFilter extends AbstractFilter {
		use RandomService;

		/** @inheritdoc */
		public function input($value, ?array $options = null) {
			return $value;
		}

		/** @inheritdoc */
		public function output($value, ?array $options = null) {
			if (empty($value) === false) {
				return $value;
			}
			return $this->randomService->uuid($options['seed'] ?? null);
		}
	}
