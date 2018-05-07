<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\Service\Security\RandomService;
	use stdClass;

	class UuidFilter extends AbstractFilter {
		use RandomService;

		/** @inheritdoc */
		public function input($value, ?stdClass $options = null) {
			if (empty($value) === false) {
				return $value;
			}
			return $this->randomService->uuid();
		}

		/** @inheritdoc */
		public function output($value, ?stdClass $options = null) {
			return $value;
		}
	}
