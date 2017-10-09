<?php
	declare(strict_types=1);
	namespace Edde\Api\Element;

		interface IElement {
			/**
			 * get the element type according to the protocol specification
			 *
			 * @return string
			 */
			public function getType(): string;
		}
