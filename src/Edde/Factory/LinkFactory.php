<?php
declare(strict_types=1);

namespace Edde\Factory;

use Edde\Container\IContainer;

/**
 * Translate the given factory into another one.
 *
 * For example ICache can be bound to ICacheManager because it implements ICache too.
 */
class LinkFactory extends AbstractFactory {
    /** @var string */
    protected $source;
    /** @var string */
    protected $target;
    /** @var mixed */
    protected $instance;

    /**
     * A young man goes into a drug store to buy condoms.
     * The pharmacist says the condoms come in packs of 3, 9 or 12 and asks which the young man wants.
     * "Well," he said, "I've been seeing this girl for a while and she's really hot. I want the condoms because I think tonight's "the" night. We're having dinner with her parents, and then we're going out. And I've got a feeling I'm gonna get lucky after that."
     * "Once she's had me, she'll want me all the time, so you'd better give me the 12 pack."
     * The young man makes his purchase and leaves.
     * Later that evening, he sits down to dinner with his girlfriend and her parents.
     * He asks if he might give the blessing and they agree.
     * He begins the prayer, but continues praying for several minutes.
     * The girl leans over to him and says, "You never told me that you were such a religious person."
     * The boy leans over to her and whispers, "You never told me that your father is a pharmacist."
     *
     * @param string $source
     * @param string $target
     */
    public function __construct(string $source, string $target) {
        $this->source = $source;
        $this->target = $target;
    }

    /** @inheritdoc */
    public function canHandle(IContainer $container, string $dependency): bool {
        return $this->source === $dependency;
    }

    /** @inheritdoc */
    public function getFactory(IContainer $container): IFactory {
        return $container->getFactory($this->target, $this->source);
    }

    /** @inheritdoc */
    public function getReflection(IContainer $container, string $dependency): IReflection {
        if ($this->instance) {
            return new Reflection();
        }
        return $this->getFactory($container)->getReflection($container, $dependency);
    }

    /** @inheritdoc */
    public function factory(IContainer $container, array $params, IReflection $dependency, string $name = null) {
        return $this->instance ?: $this->instance = $this->getFactory($container)->factory($container, $params, $dependency, $name);
    }
}
