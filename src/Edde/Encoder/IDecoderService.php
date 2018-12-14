<?php
	declare(strict_types=1);
	namespace Edde\Encoder;

	use Edde\Configurable\IConfigurable;

	interface IDecoderService extends IConfigurable {
		/**
		 * @param string $stream
		 *
		 * @return mixed
		 */
		public function decode(string $stream);
	}
