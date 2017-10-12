<?php
	namespace Edde\Common\Request;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Element\IElement;
		use Edde\Api\Utils\Inject\StringUtils;
		use Edde\Common\Protocol\AbstractProtocolHandler;

		/**
		 * Handler for protocol to handle request/message element types.
		 */
		class RequestHandler extends AbstractProtocolHandler {
			use Container;
			use StringUtils;
			const PREG = '~(?<class>[.a-z0-9-]+)/(?<action>[a-z0-9-]+)~';

			/**
			 * @inheritdoc
			 */
			public function canHandle(IElement $element) : bool {
				$isType = in_array($element->getType(), [
					'request',
					'message',
				]);
				if ($isType === false || ($match = $this->stringUtils->match((string)$element->getAttribute('request'), self::PREG)) === null) {
					return false;
				}
				$element->setMeta('::class', $class = str_replace([
					' ',
					'-',
				], [
					'\\',
					'',
				], $this->stringUtils->capitalize(str_replace('.', ' ', $match['class']))));
				$element->setMeta('::method', $this->stringUtils->toCamelHump($match['action']));
				return $this->container->canHandle($class);
			}

			/**
			 * @inheritdoc
			 */
			public function execute(IElement $element) : ?IElement {
				/** @var $instance IConfigurable */
				if (($instance = $this->container->create((string)$element->getMeta('::class'), [], static::class)) instanceof IConfigurable) {
					$instance->setup();
				}
				return $instance->{(string)$element->getMeta('::method')}($element);
			}
		}
