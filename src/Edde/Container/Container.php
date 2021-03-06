<?php
declare(strict_types=1);

namespace Edde\Container;

use Edde\Configurable\IConfigurable;
use Edde\Factory\ClassFactory;
use Edde\Factory\IFactory;
use Edde\Factory\IReflection;
use SplStack;

/**
 * One day, Little Johnny saw his grandpa smoking his cigarettes. Little Johnny asked,
 * "Grandpa, can I smoke some of your cigarettes?" His grandpa replied,
 * "Can your penis reach your asshole?"
 * "No", said Little Johnny.
 * His grandpa replied,
 * "Then you're not old enough."
 *
 * The next day, Little Johnny saw his grandpa drinking beer. He asked,
 * "Grandpa, can I drink some of your beer?"
 * His grandpa replied,
 * "Can your penis reach your asshole?"
 * "No" said Little Johhny.
 * "Then you're not old enough." his grandpa replied.
 *
 * The next day, Little Johnny was eating cookies.
 * His grandpa asked, "Can I have some of your cookies?"
 * Little Johnny replied, "Can your penis reach your asshole?"
 * His grandpa replied, "It most certainly can!"
 * Little Johnny replied, "Then go fuck yourself.
 */
class Container extends AbstractContainer {
    /** @var SplStack */
    protected $stack;
    /** @var IReflection[] */
    protected $autowires;

    public function __construct() {
        parent::__construct();
        $this->stack = new SplStack();
        $this->factories = [];
        $this->autowires = [];
    }

    /** @inheritdoc */
    public function getFactory(string $dependency, string $source = null): IFactory {
        /**
         * searching for dependency could be quite expensive task, so it's necessary
         * to cache results; real cache is not used to keep container implementation
         * as simple as possible
         */
        if (isset($this->factories[$dependencyId = 'dependency:' . $dependency])) {
            return $this->factories[$dependencyId];
        }
        /**
         * container is able to handle dynamic dependencies without forcing user to implement all
         * factories for all classes; that means container must find factory for the given
         * dependency name
         */
        foreach ($this->factories as $factory) {
            if ($factory->canHandle($this, $dependency)) {
                return $this->factories[$dependencyId] = $factory->getFactory($this);
            }
        }
        /**
         * no factory is able to handle given dependency name, oops
         */
        throw new ContainerException(sprintf('Unknown factory [%s] for dependency [%s]%s.', $dependency, $source ?: 'unknown source', $this->stack->isEmpty() ? '' : '; dependency chain [' . implode('→', array_reverse(iterator_to_array($this->stack))) . ']'));
    }

    /** @inheritdoc */
    public function factory(IFactory $factory, string $name, array $params = [], string $source = null) {
        try {
            /**
             * track current name of requested dependency; in common all dependencies should be named
             */
            $this->stack->push($name);
            /**
             * this is an optimization for dependency creation when a factory could return an instance without
             * analyzing dependencies (or in singleton case it could return singleton directly)
             */
            if (($instance = $factory->fetch($this, $name, $params)) !== null) {
                return $instance;
            }
            /**
             * the expensive part: analyze dependencies (create reflection object), create a dependency itself
             * and autowire rest of dependencies (property/method/lazy injects); the factory also could save current
             * instance under given id
             */
            return $factory->push($this, $this->dependency($factory->factory($this, $params, $reflection = $factory->getReflection($this, $name), $name), $reflection));
        } finally {
            $this->stack->pop();
        }
    }

    /** @inheritdoc */
    public function inject($instance) {
        /**
         * expensive trick to inject dependencies to an object; class factory is responsible to analyze the dependency, container is than responsible to do the rest of job
         */
        return
            is_object($instance) ?
                $this->dependency($instance, $this->autowires[$class = get_class($instance)] ?? $this->autowires[$class] = (new ClassFactory())->getReflection($this, $class)) :
                $instance;
    }

    /** @inheritdoc */
    public function dependency($instance, IReflection $reflection) {
        /**
         * formally keep condition to ensure typehint
         */
        if ($instance instanceof IAutowire) {
            $instance->autowires($reflection->getInjects(), $this);
        }
        /**
         * support for dedicated configuration of a dependency
         *
         * @var $instance IConfigurable
         */
        if ($instance instanceof IConfigurable) {
            $configurators = [];
            /**
             * dependency could have more names for configurators (for example Foo class could implements
             * IFoo; analysis could return both names for configurator)
             */
            foreach ($reflection->getConfigurators() as $configurator) {
                $configurators = array_merge($configurators, $this->configurators[$configurator] ?? []);
            }
            $instance->setConfigurators($configurators);
            /**
             * late constructor phase; all internal dependencies are available, object could do all necessary
             * steps to be prepared, but it should do only as simple things as possible (the rule is to keep
             * object serializable for eventual cache)
             */
            $instance->init();
        }
        /**
         * tradaaa!
         */
        return $instance;
    }
}
