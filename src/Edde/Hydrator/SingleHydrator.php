<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	use function reset;

	class SingleHydrator extends AbstractHydrator {
		/** @inheritdoc */
		public function hydrate(array $source) {
			return reset($source);
		}
	}