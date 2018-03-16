<?php
	declare(strict_types=1);
	namespace Edde\Ext\Validator;

	use Edde\Api\Validator\IValidatorManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Validator\BoolValidator;
	use Edde\Common\Validator\DateTimeValidator;
	use Edde\Common\Validator\EmailValidator;
	use Edde\Common\Validator\FloatValidator;
	use Edde\Common\Validator\IntegerValidator;
	use Edde\Common\Validator\RequiredValidator;
	use Edde\Common\Validator\ScalarValidator;
	use Edde\Common\Validator\StringValidator;
	use Edde\Exception\Container\ContainerException;
	use Edde\Exception\Container\FactoryException;
	use Edde\Ext\Bus\Validator\EventValidator;
	use Edde\Ext\Bus\Validator\MessageValidator;
	use Edde\Ext\Bus\Validator\RequestValidator;
	use Edde\Ext\Schema\Validator\SchemaValidator;
	use Edde\Inject\Container\Container;

	class ValidatorManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param $instance IValidatorManager
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
				'email'                    => new EmailValidator(),
				'schema'                   => $this->container->create(SchemaValidator::class, [], __METHOD__),
				'message-bus:type:message' => new MessageValidator(),
				'message-bus:type:event'   => new EventValidator(),
				'message-bus:type:request' => new RequestValidator(),
			]);
		}
	}
