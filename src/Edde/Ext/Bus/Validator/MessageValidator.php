<?php
	declare(strict_types=1);
	namespace Edde\Ext\Bus\Validator;

	use Edde\Api\Bus\IElement;
	use Edde\Api\Validator\Exception\ValidationException;
	use Edde\Common\Validator\AbstractValidator;

	class MessageValidator extends AbstractValidator {
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
			} else if ($value->hasAttribute('version') === false) {
				throw new ValidationException(
					sprintf('A message [%s] has missing "version" attribute!', get_class($value)),
					$options['::name'] ?? null
				);
			}
		}
	}
