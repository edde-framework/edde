<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Config\AbstractConfigurator;
	use Edde\Container\ContainerException;
	use Edde\Service\Container\Container;

	class ValidatorManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param $instance IValidatorManager
		 *
		 * @throws ContainerException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerValidators([
				'int'            => $validator = $this->container->create(IntValidator::class, [], __METHOD__),
				'storage:int'    => $validator,
				'string'         => $validator = $this->container->create(StringValidator::class, [], __METHOD__),
				'storage:string' => $validator,
				'bool'           => $validator = $this->container->create(BoolValidator::class, [], __METHOD__),
				'storage:bool'   => $validator,
			]);
		}
	}
