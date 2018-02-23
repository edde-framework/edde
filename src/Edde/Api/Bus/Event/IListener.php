<?php
	declare(strict_types=1);
	namespace Edde\Api\Bus\Event;

	use Edde\Api\Config\IConfigurable;

	interface IListener extends IConfigurable {
		/**
		 * return listeners; key should be event name, value listener callback
		 *
		 * @return iterable
		 */
		public function getListeners(): iterable;
	}
