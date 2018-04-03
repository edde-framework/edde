<?php
	declare(strict_types=1);
	namespace Edde\Bus;

	interface IListener {
		/**
		 * return listeners; key should be event name, value listener callback
		 *
		 * @return iterable
		 */
		public function getListeners(): iterable;
	}
