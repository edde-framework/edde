<?php
	declare(strict_types=1);
	namespace Edde\Common\Protocol;

	use Edde\Api\Element\IElement;
	use Edde\Api\Protocol\IProtocolService;
	use Edde\Common\Object\Object;

	class ProtocolService extends Object implements IProtocolService {
		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element): ?IElement {
		}
	}
