<?php
declare(strict_types=1);

namespace Edde\Validator;

use Edde\Configurable\AbstractConfigurator;

class ValidatorManagerConfigurator extends AbstractConfigurator {
    /**
     * @param $instance IValidatorManager
     */
    public function configure($instance) {
        parent::configure($instance);
        $dummy = new DummyValidator();
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
            'uuid'             => $validator = new UuidValidator(),
            'storage:uuid'     => $validator,
            'storage:json'     => $dummy,
            'storage:binary'   => $dummy,
            'storage:base64'   => $dummy,
        ]);
    }
}
