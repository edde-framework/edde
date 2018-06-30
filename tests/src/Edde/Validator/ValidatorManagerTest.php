<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Service\Validator\ValidatorManager;
	use Edde\TestCase;

	class ValidatorManagerTest extends TestCase {
		use ValidatorManager;

		/**
		 * @throws ValidatorException
		 */
		public function testValidatorException() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Requested unknown validator [nope].');
			$this->validatorManager->validate('nope', false);
		}
	}
