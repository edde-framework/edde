<?php
	declare(strict_types=1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;
	use Edde\Api\Container\IFactory;
	use Edde\Common\Object;

	/**
	 * Basic implementation for all dependency factories.
	 */
	abstract class AbstractFactory extends Object implements IFactory {
		/**
		 * @inheritdoc
		 */
		public function getFactory(IContainer $container): IFactory {
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function fetch(IContainer $container, string $id) {
		}

		/**
		 * @inheritdoc
		 */
		public function push(IContainer $container, string $id, $instance) {
			return $instance;
		}

		protected function parameters(IContainer $container, array $parameterList, IDependency $dependency, string $name = null) {
			$grab = count($parameterList);
			$dependencyList = [];
			foreach ($dependency->getParameterList() as $reflectionParameter) {
				if (--$grab >= 0 || $reflectionParameter->isOptional()) {
					continue;
				}
				$dependencyList[] = $container->factory($container->getFactory($class = (($class = $reflectionParameter->getClass()) ? $class : $reflectionParameter->getName()), $name), [], $class, $name);
			}
			return array_merge($parameterList, $dependencyList);
		}
	}
