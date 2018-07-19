<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\TestCase;

	class IntValidatorException extends TestCase {
		/**
		 * @throws ValidatorException
		 */
		public function testValidator() {
			$validator = new IntValidator();
			$validator->validate(0);
			$validator->validate(100);
			self::assertTrue(true, 'no errors reported!');
		}

		/**
		 * @throws ValidatorException
		 */
		public function testFloatException() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Value [nope] is not integer.');
			$validator = new IntValidator();
			$validator->validate(3.14, ['name' => 'nope']);
		}

		/**
		 * @throws ValidatorException
		 */
		public function testException() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Value [name of validated value] is not integer.');
			$validator = new IntValidator();
			$validator->validate('fujky', ['name' => 'name of validated value']);
		}
	}
