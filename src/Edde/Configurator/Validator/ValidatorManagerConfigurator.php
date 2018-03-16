<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Validator;

	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Exception\Container\ContainerException;
	use Edde\Exception\Container\FactoryException;
	use Edde\Ext\Bus\Validator\EventValidator;
	use Edde\Ext\Bus\Validator\MessageValidator;
	use Edde\Ext\Bus\Validator\RequestValidator;
	use Edde\Inject\Container\Container;
	use Edde\Validator\BoolValidator;
	use Edde\Validator\DateTimeValidator;
	use Edde\Validator\FloatValidator;
	use Edde\Validator\IntegerValidator;
	use Edde\Validator\RequiredValidator;
	use Edde\Validator\ScalarValidator;
	use Edde\Validator\StringValidator;

	class ValidatorManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param $instance \Edde\Validator\IValidatorManager
		 *
		 * @throws ContainerException
		 * @throws FactoryException
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
				'message-bus:type:message' => new MessageValidator(),
				'message-bus:type:event'   => new EventValidator(),
				'message-bus:type:request' => new RequestValidator(),
			]);
		}
	}
