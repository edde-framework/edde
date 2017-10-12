<?php
	namespace Edde\Common\Element;

		use Edde\Api\Element\IElement;
		use Edde\Common\Node\Node;

		class Element extends Node implements IElement {
			public function __construct(string $type) {
				parent::__construct(null, null, ['type' => $type]);
			}

			/**
			 * @inheritdoc
			 */
			public function getType() : string {
				return $this->getAttribute('type');
			}
		}
