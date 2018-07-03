<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol\Request;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Common\Protocol\Request\AbstractRequestHandler;

	/**
	 * Request handler connected to container.
	 */
	class ContainerRequestHandler extends AbstractRequestHandler {
		use LazyContainerTrait;

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			if (parent::canHandle($element) === false || strpos($request = $element->getAttribute('request'), '::') === false) {
				return false;
			}
			list($name, $method) = explode('::', $request);
			$element->setMeta('::class', $name);
			$element->setMeta('::method', $method);
			return method_exists($name, $method);
		}
	}
