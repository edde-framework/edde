<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\TestCase;

	class DateTimeValidatorTest extends TestCase {
		/**
		 * @throws ValidatorException
		 */
		public function testException() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Value [name of validated value] is not instanceof DateTime.');
			$validator = new DateTimeValidator();
			$validator->validate('fujky', ['name' => 'name of validated value']);
		}
	}
