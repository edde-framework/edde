<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Edde;
	use stdClass;
	use function property_exists;
	use function sprintf;

	abstract class AbstractValidator extends Edde implements IValidator {
		/**
		 * @param null|stdClass $options
		 *
		 * @return string
		 */
		protected function getValueName(?stdClass $options): string {
			return $options && property_exists($options, 'name') ? (string)$options->name : '<unknown value name, use name in validator $options to set name>';
		}

		/**
		 * @param mixed         $value
		 * @param null|stdClass $options
		 *
		 * @return bool true means value is available (!==null)
		 *
		 * @throws ValidatorException
		 */
		protected function checkRequired($value, ?stdClass $options): bool {
			if ($value === null && ($options->required ?? true) === true) {
				throw new ValidatorException(sprintf('Required value [%s] is null.', $this->getValueName($options)));
			}
			return $value !== null;
		}
	}
