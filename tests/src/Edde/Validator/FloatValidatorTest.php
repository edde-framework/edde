<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\TestCase;

	class FloatValidatorTest extends TestCase {
		/**
		 * @throws ValidatorException
		 */
		public function testValidator() {
			$validator = new FloatValidator();
			$validator->validate(3.14, ['name' => 'name of validated value']);
			self::assertTrue(true, 'no error reported :)!');
		}

		/**
		 * @throws ValidatorException
		 */
		public function testException() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Value [name of validated value] is not float.');
			$validator = new FloatValidator();
			$validator->validate('fujky', ['name' => 'name of validated value']);
		}
	}
