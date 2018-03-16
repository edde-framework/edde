<?php
	declare(strict_types=1);
	namespace Edde\Bus;

	use Edde\Config\IConfigurable;

	interface IListener extends IConfigurable {
		/**
		 * return listeners; key should be event name, value listener callback
		 *
		 * @return iterable
		 */
		public function getListeners(): iterable;
	}
