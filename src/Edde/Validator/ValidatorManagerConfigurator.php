<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Config\AbstractConfigurator;

	class ValidatorManagerConfigurator extends AbstractConfigurator {
		/**
		 * @param $instance IValidatorManager
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerValidators([
				'int'              => $validator = new IntValidator(),
				'storage:int'      => $validator,
				'float'            => $validator = new FloatValidator(),
				'storage:float'    => $validator,
				'string'           => $validator = new StringValidator(),
				'storage:string'   => $validator,
				'bool'             => $validator = new BoolValidator(),
				'storage:bool'     => $validator,
				'datetime'         => $validator = new DateTimeValidator(),
				'storage:datetime' => $validator,
				'DateTime'         => $validator,
				'storage:DateTime' => $validator,
			]);
		}
	}
