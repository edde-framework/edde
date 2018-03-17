<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Inject\Validator\ValidatorManager;
	use Edde\TestCase;

	class ValidatorManagerTest extends TestCase {
		use ValidatorManager;

		/**
		 * @throws ValidationException
		 * @throws ValidatorException
		 */
		public function testUnknownValidator() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Requested unknown validator [boom].');
			$this->validatorManager->check('boom', 'boom');
		}

		/**
		 * @throws ValidationException
		 * @throws ValidatorException
		 */
		public function testBooleanValidatorFail() {
			$this->expectException(ValidationException::class);
			$this->expectExceptionMessage('Given value of type [integer] is not a boolean!');
			$this->validatorManager->check('type:bool', 1);
		}

		/**
		 * @throws ValidatorException
		 */
		public function testBooleanValidator() {
			self::assertTrue($this->validatorManager->getValidator('type:bool')->isValid(true));
		}

		/**
		 * @throws ValidationException
		 * @throws ValidatorException
		 */
		public function testStringValidator() {
			$this->expectException(ValidationException::class);
			$this->expectExceptionMessage('Given value [pi value] of type [double] is not a string!');
			$this->validatorManager->check('type:string', 3.14, ['::name' => 'pi value']);
		}

		/**
		 * @throws ValidatorException
		 */
		public function testFloatValidator() {
			self::assertTrue($this->validatorManager->getValidator('type:double')->isValid(3.14));
			self::assertTrue($this->validatorManager->getValidator('type:float')->isValid(3.14));
		}

		/**
		 * @throws ValidatorException
		 */
		public function testIntValidator() {
			self::assertTrue($this->validatorManager->getValidator('type:int')->isValid(314));
			self::assertFalse($this->validatorManager->getValidator('type:int')->isValid(3.14));
		}

		/**
		 * @throws ValidatorException
		 */
		public function testEmailValidator() {
			self::assertTrue($this->validatorManager->getValidator('email')->isValid('a@b.c'));
			self::assertFalse($this->validatorManager->getValidator('email')->isValid('hovno'));
			self::assertFalse($this->validatorManager->getValidator('email')->isValid(12));
		}
	}
