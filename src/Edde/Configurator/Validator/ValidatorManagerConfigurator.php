<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Validator;

	use Edde\Config\AbstractConfigurator;
	use Edde\Service\Container\Container;
	use Edde\Validator\BoolValidator;
	use Edde\Validator\DateTimeValidator;
	use Edde\Validator\FloatValidator;
	use Edde\Validator\IntegerValidator;
	use Edde\Validator\IValidatorManager;
	use Edde\Validator\RequiredValidator;
	use Edde\Validator\ScalarValidator;
	use Edde\Validator\StringValidator;

	class ValidatorManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IValidatorManager $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerValidators([
				'type:bool'                => $validator = new BoolValidator(),
				'type:boolean'             => $validator,
				'type:string'              => $validator = new StringValidator(),
				'type:str'                 => $validator,
				'type:integer'             => $validator = new IntegerValidator(),
				'type:int'                 => $validator,
				'type:float'               => $validator = new FloatValidator(),
				'type:double'              => $validator,
				'type:DateTime'            => $validator = new DateTimeValidator(),
				'type:scalar'              => new ScalarValidator(),
				'required'                 => new RequiredValidator(),
				'email'                    => new \Edde\Validator\EmailValidator(),
				'schema'                   => $this->container->create(\Edde\Validator\SchemaValidator::class, [], __METHOD__),
				'message-bus:type:message' => new \Edde\Validator\Bus\MessageValidator(),
				'message-bus:type:event'   => new \Edde\Validator\Bus\EventValidator(),
				'message-bus:type:request' => new \Edde\Validator\Bus\RequestValidator(),
			]);
		}
	}