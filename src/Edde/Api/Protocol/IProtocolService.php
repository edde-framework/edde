<?php
	declare(strict_types=1);
	namespace Edde\Api\Protocol;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Element\IElement;

	interface IProtocolService extends IConfigurable {
		/**
		 * execute the given element and return eventual result
		 *
		 * @param IElement $element
		 *
		 * @return IElement|null
		 */
		public function execute(IElement $element): ?IElement;
	}
