<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	interface IHydrator {
		/**
		 * hydrate the given input (row, record) to (arbitrary) output
		 *
		 * @param array $source
		 *
		 * @return mixed
		 */
		public function hydrate(array $source);
	}
