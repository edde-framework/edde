<?php
	declare(strict_types=1);
	namespace Edde\Generator;

	use Edde\Service\Crypt\RandomService;

	class UuidGenerator extends AbstractGenerator {
		use RandomService;

		/** @inheritdoc */
		public function generate(array $options = []) {
			return $this->randomService->uuid();
		}
	}
