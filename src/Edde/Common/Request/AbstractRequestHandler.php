<?php
	declare(strict_types=1);

	namespace Edde\Common\Request;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Request\IRequestHandler;
	use Edde\Common\Protocol\AbstractProtocolHandler;

	abstract class AbstractRequestHandler extends AbstractProtocolHandler implements IRequestHandler {
		use Container;

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			return in_array($element->getType(), [
				'request',
				'message',
			]);
		}

		/**
		 * @inheritdoc
		 */
		public function onExecute(IElement $element) {
			/** @var $instance IConfigurable */
			if (($instance = $this->container->create((string)$element->getMeta('::class'), [], static::class)) instanceof IConfigurable) {
				$instance->setup();
			}
			return $instance->{(string)$element->getMeta('::method')}($element);
		}
	}
