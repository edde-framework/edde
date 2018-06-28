<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	use Edde\Configurable\IConfigurable;

	interface IHydratorManager extends IConfigurable {
		/**
		 * return simple schema hydrator able to process just schema of the given name
		 *
		 * @param string $name
		 *
		 * @return IHydrator
		 */
		public function schema(string $name = null): IHydrator;

		/**
		 * return hydrator returning just very first item from the result
		 *
		 * @return IHydrator
		 */
		public function single(): IHydrator;
	}
