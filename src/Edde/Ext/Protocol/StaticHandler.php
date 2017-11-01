<?php
	declare(strict_types=1);
	namespace Edde\Ext\Protocol;

		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Element\IElement;
		use Edde\Common\Protocol\AbstractProtocolHandler;

		/**
		 * Handles all elements with "::class" and "::method" meta attributes filled.
		 */
		class StaticHandler extends AbstractProtocolHandler {
			use Container;

			/**
			 * @param IElement $element
			 *
			 * @return bool
			 */
			public function canHandle(IElement $element) : bool {
				if (($metaList = $element->getMetaList())->has('::method') === false || ($class = $metaList->get('::class', false)) === false) {
					return false;
				}
				return $this->container->canHandle($class);
			}
		}
