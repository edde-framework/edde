<?php
	declare(strict_types=1);
	namespace Edde\Common\Protocol;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Element\IElement;
		use Edde\Api\Protocol\Exception\IncompatibleElementException;
		use Edde\Api\Protocol\IProtocolHandler;
		use Edde\Common\Object\Object;

		abstract class AbstractProtocolHandler extends Object implements IProtocolHandler {
			use Container;

			/**
			 * @inheritdoc
			 */
			public function check(IElement $element) : IProtocolHandler {
				if ($this->canHandle($element)) {
					return $this;
				}
				throw new IncompatibleElementException(sprintf('Unsupported element [%s] in protocol handler [%s].', $element->getName(), static::class));
			}

			/**
			 * @inheritdoc
			 */
			public function execute(IElement $element) : ?IElement {
				/** @var $instance IConfigurable */
				if (($instance = $this->container->create((string)$element->getMeta('::class'), [], static::class)) instanceof IConfigurable && $instance->isSetup() === false) {
					$instance->setup();
				}
				return $instance->{(string)$element->getMeta('::method')}($element);
			}
		}
