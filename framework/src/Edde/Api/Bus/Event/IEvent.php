<?php
	declare(strict_types=1);
	namespace Edde\Api\Bus\Event;

	use Edde\Api\Bus\IElement;

	interface IEvent extends IElement {
		/**
		 * return event name
		 *
		 * @return string
		 */
		public function getEvent(): string;
	}
