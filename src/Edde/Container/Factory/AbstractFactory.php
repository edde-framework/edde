<?php
	declare(strict_types=1);
	namespace Edde\Container\Factory;

	use Edde\Container\ContainerException;
	use Edde\Container\IContainer;
	use Edde\Container\IFactory;
	use Edde\Container\IReflection;
	use Edde\Object;

	/**
	 * Basic implementation for all dependency factories.
	 */
	abstract class AbstractFactory extends Object implements IFactory {
		/** @inheritdoc */
		public function getFactory(IContainer $container): IFactory {
			return $this;
		}

		/** @inheritdoc */
		public function fetch(IContainer $container, string $name, array $parameterList) {
		}

		/** @inheritdoc */
		public function push(IContainer $container, $instance) {
			return $instance;
		}

		/**
		 * @param IContainer  $container
		 * @param array       $parameterList
		 * @param IReflection $reflection
		 * @param string|null $name
		 *
		 * @return array
		 *
		 * @throws ContainerException
		 */
		protected function parameters(IContainer $container, array $parameterList, IReflection $reflection, string $name = null) {
			$grab = count($parameterList);
			$dependencyList = [];
			foreach ($reflection->getParams() as $parameter) {
				if (--$grab >= 0 || $parameter->isOptional()) {
					continue;
				}
				$dependencyList[] = $container->factory($container->getFactory($class = $parameter->getClass(), $name), $class, [], $name);
			}
			return array_merge($parameterList, $dependencyList);
		}
	}
