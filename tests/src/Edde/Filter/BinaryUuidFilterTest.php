<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\TestCase;
	use function base64_decode;
	use function base64_encode;

	class BinaryUuidFilterTest extends TestCase {
		public function testInput() {
			$input = new BinaryUuidFilter();
			self::assertEquals('JlRbOSUbQWqXjOrS6yv+rw==', base64_encode($input->input('26545b39-251b-416a-978c-ead2eb2bfeaf')));
		}

		public function testOutput() {
			$input = new BinaryUuidFilter();
			self::assertEquals('26545b39-251b-416a-978c-ead2eb2bfeaf', $input->output(base64_decode('JlRbOSUbQWqXjOrS6yv+rw==')));
		}
	}
