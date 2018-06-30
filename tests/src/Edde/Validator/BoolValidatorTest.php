<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\TestCase;

	class BoolValidatorTest extends TestCase {
		/**
		 * @throws ValidatorException
		 */
		public function testException() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Value [name of validated value] is not boolean.');
			$validator = new BoolValidator();
			$validator->validate('nope', ['name' => 'name of validated value']);
		}
	}
