<?php
	declare(strict_types=1);
	namespace Edde\Common\Generator;

	class DateTimeGenerator extends AbstractGenerator {
		/** @inheritdoc */
		public function generate(array $options = []) {
			return new \DateTime();
		}
	}
