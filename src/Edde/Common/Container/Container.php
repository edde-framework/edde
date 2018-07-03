<?php
	declare(strict_types=1);

	namespace Edde\Common\Container;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\Exception\UnknownFactoryException;
	use Edde\Api\Container\IAutowire;
	use Edde\Api\Container\IDependency;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Reflection\IReflectionParameter;
	use Edde\Common\Container\Factory\ClassFactory;

	/**
	 * Default implementation of a dependency container.
	 */
	class Container extends AbstractContainer {
		/**
		 * @var \SplStack
		 */
		protected $stack;
		/**
		 * @var IFactory[]
		 */
		protected $factoryMap;
		/**
		 * @var IDependency
		 */
		protected $autowireList;

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
		public function __construct() {
			/**
			 * stack to track list of dependencies
			 */
			$this->stack = new \SplStack();
			$this->factoryMap = [];
			$this->autowireList = [];
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function getFactory(string $dependency, string $source = null): IFactory {
			/**
			 * searching for dependency could be quite expensive task, so it's necessary
			 * to cache results; real cache is not used to keep container implementation
			 * as simple as possible
			 */
			if (isset($this->factoryMap[$dependency])) {
				return $this->factoryMap[$dependency];
			}
			/**
			 * container is able to handle dynamic dependencies without forcing user to implement all
			 * factories for all classes; that means container must find factory for the given
			 * dependency name
			 */
			foreach ($this->factoryList as $factory) {
				if ($factory->canHandle($this, $dependency)) {
					return $this->factoryMap[$dependency] = $factory->getFactory($this);
				}
			}
			/**
			 * no factory is able to handle given dependency name, oops
			 */
			throw new UnknownFactoryException(sprintf('Unknown factory [%s] for dependency [%s]%s.', $dependency, $source ?: 'unknown source', $this->stack->isEmpty() ? '' : '; dependency chain [' . implode('→', array_reverse(iterator_to_array($this->stack))) . ']'));
		}

		/**
		 * @inheritdoc
		 */
		public function factory(IFactory $factory, array $parameterList = [], string $name = null, string $source = null) {
			try {
				/**
				 * track current name of requested dependency; in common almost all dependencies should be named
				 */
				$this->stack->push($name ?: '[no name]');
				/**
				 * this is an optimization for dependency creation when a factory could return instance without
				 * analyzing dependencies (or in singleton case it could return singleton directly)
				 */
				if (($instance = $factory->fetch($this, $fetchId = (get_class($factory) . count($parameterList) . $name . $source))) !== null) {
					return $instance;
				}
				/**
				 * the expensive part: analyze dependencies (create IDependency object), create a dependency itself
				 * and autowire rest of dependencies (property/method/lazy injects); the factory also could save current
				 * instance under given id
				 */
				return $factory->push($this, $fetchId, $this->dependency($instance = $factory->execute($this, $parameterList, $dependency = $factory->createDependency($this, $name), $name), $dependency));
			} finally {
				$this->stack->pop();
			}
		}

		/**
		 * @inheritdoc
		 */
		public function inject($instance, bool $force = false) {
			/**
			 * expensive trick to inject dependencies to an object; class factory is responsible to analyze the dependency, container is than responsible to do the rest of job
			 */
			return is_object($instance) ? $this->dependency($instance, $this->autowireList[$class = get_class($instance)] ?? $this->autowireList[$class] = (new ClassFactory())->createDependency($this, $class), $force !== true) : $instance;
		}

		/**
		 * @inheritdoc
		 */
		public function dependency($instance, IDependency $dependency, bool $lazy = true) {
			if (is_object($instance) === false) {
				return $instance;
			}
			/**
			 * quite obvious autowire: do static and lazy injects, depends on lazy flag
			 */
			if ($instance instanceof IAutowire) {
				$class = get_class($instance);
				$lazyList = $dependency->getLazyList();
				/** @var $instance IAutowire */
				/** @var $reflectionParameter IReflectionParameter */
				/**
				 * a trick to remove duplicated code - if we are not lazy, autowire all dependencies
				 */
				foreach (array_merge($dependency->getInjectList(), $lazy ? [] : $lazyList) as $reflectionParameter) {
					/**
					 * it's important to keep all parameters there to keep track of dependency chain in case of an exception
					 */
					$instance->autowire($reflectionParameter->getName(), $this->create($reflectionParameter->getClass(), [], $class));
				}
				/**
				 * do lazy autowiring if the $lazy flag is not false
				 */
				foreach ($lazy ? $lazyList : [] as $reflectionParameter) {
					$instance->lazy($reflectionParameter->getName(), $this, $reflectionParameter->getClass());
				}
			}
			/**
			 * support for dedicated configuration of a dependency
			 *
			 * @var $instance IConfigurable
			 */
			if ($instance instanceof IConfigurable) {
				$configuratorList = [];
				/**
				 * dependency could have more names for configurators (for example Foo class could implements
				 * IFoo; analysis could return both names for configurator)
				 */
				foreach ($dependency->getConfiguratorList() as $configurator) {
					$configuratorList = array_merge($configuratorList, $this->configuratorList[$configurator] ?? []);
				}
				$instance->setConfiguratorList($configuratorList);
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
