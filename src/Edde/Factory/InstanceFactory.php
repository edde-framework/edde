<?php
declare(strict_types=1);

namespace Edde\Factory;

use Edde\Container\IContainer;

class InstanceFactory extends ClassFactory {
    /** @var string */
    protected $name;
    /** @var object */
    protected $instance;
    /** @var object */
    protected $current;

    /**
     * @param string $name
     * @param object $instance
     */
    public function __construct(string $name, $instance) {
        $this->name = $name;
        $this->instance = $instance;
    }

    /** @inheritdoc */
    public function getUuid(): ?string {
        return $this->name;
    }

    /** @inheritdoc */
    public function canHandle(IContainer $container, string $dependency): bool {
        return $this->name === $dependency;
    }

    /** @inheritdoc */
    public function fetch(IContainer $container, string $name, array $params) {
        return $this->current;
    }

    /** @inheritdoc */
    public function push(IContainer $container, $instance) {
        return $this->current = $instance;
    }

    /** @inheritdoc */
    public function factory(IContainer $container, array $params, IReflection $dependency, string $name = null) {
        return $this->instance;
    }
}
