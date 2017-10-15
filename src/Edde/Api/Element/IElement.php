<?php
	declare(strict_types=1);
	namespace Edde\Api\Element;

		use Edde\Api\Node\INode;

		/**
		 * Element is formal interface for the core object of The Protocol specification.
		 */
		interface IElement extends INode {
			/**
			 * get the element type according to the protocol specification
			 *
			 * @return string
			 */
			public function getType(): string;
		}
