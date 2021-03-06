<?php
declare(strict_types=1);

namespace Edde\Factory;

use Edde\Container\IContainer;

/**
 * Interface to class binding factory.
 *
 * Practical thought:
 * A husband is supposed to make his wife's panties wet, not her eyes.
 * A wife is supposed to make her husband's dick hard, not his life...!
 */
class InterfaceFactory extends ClassFactory {
    /** @var string */
    protected $interface;
    /**  @var string */
    protected $class;
    /** @var mixed */
    protected $instance;

    /**
     * @param string $interface
     * @param string $class
     */
    public function __construct(string $interface, string $class) {
        $this->interface = $interface;
        $this->class = $class;
    }

    /** @inheritdoc */
    public function getUuid(): ?string {
        return $this->interface;
    }

    /** @inheritdoc */
    public function canHandle(IContainer $container, string $dependency): bool {
        return $dependency === $this->interface;
    }

    /** @inheritdoc */
    public function getReflection(IContainer $container, string $dependency): IReflection {
        if ($this->instance) {
            return new Reflection();
        }
        return parent::getReflection($container, $this->class);
    }

    /** @inheritdoc */
    public function fetch(IContainer $container, string $name, array $params) {
        return $this->instance;
    }

    /** @inheritdoc */
    public function factory(IContainer $container, array $params, IReflection $dependency, string $name = null) {
        return $this->instance ?: $this->instance = parent::factory($container, $params, $dependency, $this->class);
    }
}
