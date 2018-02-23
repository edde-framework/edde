<?php
	declare(strict_types=1);
	namespace Edde\Common\Bus;

	use Edde\Api\Bus\Exception\InvalidElementException;
	use Edde\Api\Bus\Exception\UnhandledElementException;
	use Edde\Api\Bus\IElement;
	use Edde\Api\Bus\IHandler;
	use Edde\Api\Validator\Exception\UnknownValidatorException;
	use Edde\Api\Validator\Exception\ValidationException;
	use Edde\Api\Validator\Inject\ValidatorManager;
	use Edde\Common\Object\Object;

	abstract class AbstractHandler extends Object implements IHandler {
		use ValidatorManager;

		/** @inheritdoc */
		public function send(IElement $element): IElement {
			$this->validate($element);
			return $this->onSend($element);
		}

		/** @inheritdoc */
		public function validate(IElement $element): void {
			if ($this->canHandle($element) === false) {
				throw new UnhandledElementException(sprintf('Cannot handle message type [%s (%s)] message handler [%s].', $element->getType(), get_class($element), static::class));
			}
			$this->onValidate($element);
		}

		/**
		 * @param IElement $element
		 *
		 * @throws UnknownValidatorException
		 * @throws ValidationException
		 */
		protected function onValidate(IElement $element): void {
			$this->validatorManager->getValidator('message-bus:type:' . $element->getType())->validate($element);
		}

		/**
		 * @param IElement $element
		 *
		 * @return IElement
		 * @throws ValidationException
		 * @throws InvalidElementException
		 */
		protected function onSend(IElement $element): IElement {
			return $this->execute($element);
		}
	}