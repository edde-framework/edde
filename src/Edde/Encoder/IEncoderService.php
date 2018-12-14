<?php
	declare(strict_types=1);
	namespace Edde\Encoder;

	use Edde\Configurable\IConfigurable;

	interface IEncoderService extends IConfigurable {
		const TYPE_NULL = 'n';
		const TYPE_BOOL_TRUE = 't';
		const TYPE_BOOL_FALSE = 'f';

		/**
		 * return encoded binary string (more stream than string)
		 *
		 * @param mixed $input
		 *
		 * @return string
		 *
		 * @throws EncoderException
		 */
		public function encode($input): string;
	}
