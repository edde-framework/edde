<?php
	declare(strict_types=1);
	namespace Edde\Encoder;

	use Edde\Service\Encoder\DecoderService;
	use Edde\Service\Encoder\EncoderService;
	use Edde\TestCase;

	class EncoderServiceTest extends TestCase {
		use EncoderService;
		use DecoderService;

		/**
		 * @throws EncoderException
		 */
		public function testEncodeDecodeScalars() {
			self::assertSame('e3' . IEncoderService::TYPE_BOOL_TRUE, $this->encoderService->encode(true));
			self::assertSame('e3' . IEncoderService::TYPE_BOOL_FALSE, $this->encoderService->encode(false));
		}
	}
