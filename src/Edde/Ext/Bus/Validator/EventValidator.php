<?php
	declare(strict_types=1);
	namespace Edde\Ext\Bus\Validator;

	use Edde\Api\Bus\IElement;
	use Edde\Api\Validator\Exception\ValidationException;
	use Edde\Common\Validator\AbstractValidator;

	class EventValidator extends AbstractValidator {
		/** @inheritdoc */
		public function validate($value, array $options = []): void {
			/** @var $value IElement */
			if (is_object($value) === false) {
				throw new ValidationException(
					sprintf('Value of type [%s] is not an instance of [%s].', gettype($value), IElement::class),
					$options['::name'] ?? null
				);
			} else if ($value instanceof IElement === false) {
				throw new ValidationException(
					sprintf('Object of type [%s] is not an instance of [%s].', get_class($value), IElement::class),
					$options['::name'] ?? null
				);
			} else if ($value->hasAttribute('event') === false) {
				throw new ValidationException(
					sprintf('An event message [%s] has missing "event" attribute!', get_class($value)),
					$options['::name'] ?? null
				);
			} else if ($value->getUuid() === '') {
				throw new ValidationException(
					sprintf('An event message [%s] has missing "uuid"!', get_class($value)),
					$options['::name'] ?? null
				);
			}
		}
	}