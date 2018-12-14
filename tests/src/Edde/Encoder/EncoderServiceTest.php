<?php
	declare(strict_types=1);
	namespace Edde\Encoder;

	use Edde\Service\Encoder\DecoderService;
	use Edde\Service\Encoder\EncoderService;
	use Edde\TestCase;

	class EncoderServiceTest extends TestCase {
		use EncoderService;
		use DecoderService;

		public function testProprietaryException() {
			$this->expectException(EncoderException::class);
			$this->expectExceptionMessage('Cannot encode proprietary class [Edde\Encoder\EncoderServiceTest]. Only scalars, arrays and plain objects are supported.');
			$this->encoderService->encode($this);
		}

		/**
		 * @throws EncoderException
		 */
		public function testEncodeDecodeScalars() {
			self::assertSame('e3n', $this->encoderService->encode(null));
			self::assertSame('e3t', $this->encoderService->encode(true));
			self::assertSame('e3f', $this->encoderService->encode(false));
			self::assertSame('e3d83.141593', $this->encoderService->encode(3.14159265));
			self::assertSame('e3i42020', $this->encoderService->encode(2020));
		}
	}
