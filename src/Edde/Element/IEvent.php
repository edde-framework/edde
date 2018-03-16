<?php
	declare(strict_types=1);
	namespace Edde\Element;

	interface IEvent extends IElement {
		/**
		 * return event name
		 *
		 * @return string
		 */
		public function getEvent(): string;
	}
