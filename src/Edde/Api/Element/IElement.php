<?php
	declare(strict_types=1);
	namespace Edde\Api\Element;

		use Edde\Api\Node\INode;

		interface IElement extends INode {
			/**
			 * get the element type according to the protocol specification
			 *
			 * @return string
			 */
			public function getType() : string;
		}
