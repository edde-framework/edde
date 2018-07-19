<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Service\Security\RandomService;
	use Edde\TestCase;

	class UuidValidatorTest extends TestCase {
		use RandomService;

		/**
		 * @throws ValidatorException
		 */
		public function testValidator() {
			$validator = new UuidValidator();
			$validator->validate('159cb406-feb3-4f33-989b-1d94608dd716', ['name' => 'first uuid']);
			$validator->validate('76252e71-c19c-4068-a28b-a9c5ba0475b9');
			$validator->validate('f1606d12-e794-4820-ae61-669928a557e4');
			$validator->validate($this->randomService->uuid());
			$validator->validate($this->randomService->uuid('seed'));
			self::assertTrue(true, 'everything is ok ;)!');
		}

		/**
		 * @throws ValidatorException
		 */
		public function testException() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Value [boom] [not valid thing, dude] is not valid uuid v4.');
			$validator = new UuidValidator();
			$validator->validate('boom', ['name' => 'not valid thing, dude']);
		}
	}
