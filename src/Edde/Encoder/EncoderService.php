<?php
	declare(strict_types=1);
	namespace Edde\Encoder;

	use Edde\Edde;
	use function gettype;

	class EncoderService extends Edde implements IEncoderService {
		/** @inheritdoc */
		public function encode($input): string {
			/**
			 * magic header
			 */
			$stream = 'e3';
			switch ($type = gettype($input)) {
				case 'boolean':
					$stream .= $this->encodeBoolean($input);
					break;
				default:
					throw new EncoderException(sprintf('Unsupported proprietary type [%s]. Only scalar types, arrays and plain objects can be serialized.', $type));
			}
			return $stream;
		}

		protected function encodeBoolean($value): string {
			return $value === true ? self::TYPE_BOOL_TRUE : self::TYPE_BOOL_FALSE;
		}
	}
