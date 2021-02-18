<?php
declare(strict_types=1);

namespace Edde\Container;

use Closure;
use Edde\Configurable\IConfigurable;
use Edde\Factory\IParameter;
use Edde\ObjectException;

trait Autowire {
    protected $tAutowires = [];

    /** @inheritdoc */
    public function autowires(array $parameters, IContainer $container) {
        /** @var $parameter IParameter */
        foreach ($parameters as $parameter) {
            $property = $parameter->getName();
            $this->tAutowires[$property] = [
                $container,
                $parameter->getClass(),
            ];
            call_user_func(Closure::bind(function (string $property) {
                unset($this->{$property});
            }, $this, static::class), $property);
        }
        return $this;
    }

    /**
     * @param string $name
     *
     * @return IConfigurable
     *
     * @throws ContainerException
     * @throws ObjectException
     */
    public function __get(string $name) {
        if (isset($this->tAutowires[$name])) {
            /** @var $container IContainer */
            [
                $container,
                $dependency,
            ] = $this->tAutowires[$name];
            /** @var $instance IConfigurable */
            if (($instance = $this->{$name} = $container->create($dependency, [], static::class)) instanceof IConfigurable && $instance->isSetup() === false) {
                $instance->setup();
            }
            return $instance;
        }
        /** @noinspection PhpUndefinedClassInspection */
        return parent::__get($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     *
     * @throws ObjectException
     */
    public function __set(string $name, $value) {
        if (isset($this->tAutowires[$name])) {
            $this->{$name} = $value;
            return $this;
        }
        /** @noinspection PhpUndefinedClassInspection */
        return parent::__set($name, $value);
    }
}
