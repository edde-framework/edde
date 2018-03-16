<?php
	declare(strict_types=1);
	namespace Edde\Generator;

	use DateTime;

	class DateTimeGenerator extends AbstractGenerator {
		/** @inheritdoc */
		public function generate(array $options = []) {
			return new DateTime();
		}
	}
