<?php
	declare(strict_types=1);
	namespace Edde\Encoder;

	use Edde\Edde;
	use Edde\Service\Utils\StringUtils;
	use stdClass;
	use function get_class;
	use function gettype;
	use function method_exists;
	use function sprintf;
	use function strlen;
	use function strtolower;

	class EncoderService extends Edde implements IEncoderService {
		use StringUtils;

		/** @inheritdoc */
		public function encode($input): string {
			return 'e3' . $this->resolve($input);
		}

		public function resolve($input): string {
			$method = sprintf('encode%s', $this->stringUtils->toCamelCase(strtolower($type = gettype($input))));
			if (method_exists($this, $method) === false) {
				throw new EncoderException(sprintf('Unsupported proprietary type [%s]. Only scalar types, arrays and plain objects can be serialized.', $type));
			}
			return (string)$this->{$method}($input);
		}

		protected function encodeObject($value): string {
			if ($value instanceof stdClass === false) {
				throw new EncoderException(sprintf('Cannot encode proprietary class [%s]. Only scalars, arrays and plain objects are supported.', get_class($value)));
			}
		}

		protected function encodeNull($value): string {
			return self::TYPE_NULL;
		}

		protected function encodeBoolean($value): string {
			return $value === true ? self::TYPE_BOOL_TRUE : self::TYPE_BOOL_FALSE;
		}

		protected function encodeDouble($value): string {
			return self::TYPE_DOUBLE . strlen($value = sprintf('%f', $value)) . $value;
		}

		protected function encodeInteger($value): string {
			return self::TYPE_INTEGER . strlen($value = sprintf('%d', $value)) . $value;
		}
	}
