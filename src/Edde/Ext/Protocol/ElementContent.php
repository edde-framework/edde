<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Common\Converter\Content;

	class ElementContent extends Content {
		public function __construct(IElement $element) {
			parent::__construct($element, IElement::class);
		}
	}
