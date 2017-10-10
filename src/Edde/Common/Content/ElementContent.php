<?php
	namespace Edde\Common\Content;

		use Edde\Api\Element\IElement;

		class ElementContent extends Content {
			public function __construct(IElement $element) {
				parent::__construct($element, 'element');
			}
		}
