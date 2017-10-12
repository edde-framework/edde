<?php
	namespace Edde\Common\Protocol;

		use Edde\Api\Element\IElement;
		use Edde\Api\Protocol\Exception\IncompatibleElementException;
		use Edde\Api\Protocol\IProtocolHandler;
		use Edde\Common\Object\Object;

		abstract class AbstractProtocolHandler extends Object implements IProtocolHandler {
			/**
			 * @inheritdoc
			 */
			public function check(IElement $element) : IProtocolHandler {
				if ($this->canHandle($element)) {
					return $this;
				}
				throw new IncompatibleElementException(sprintf('Unsupported element [%s] in protocol handler [%s].', $element->getName(), static::class));
			}
		}
