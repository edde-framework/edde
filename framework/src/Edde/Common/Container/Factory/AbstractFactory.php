<?php
	declare(strict_types=1);
	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\Exception\UnknownFactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Container\IReflection;
	use Edde\Common\Object\Object;

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
		public function fetch(IContainer $container, string $name, array $parameterList) {
		}

		/**
		 * @inheritdoc
		 */
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
		 * @throws UnknownFactoryException
		 */
		protected function parameters(IContainer $container, array $parameterList, IReflection $reflection, string $name = null) {
			$grab = count($parameterList);
			$dependencyList = [];
			foreach ($reflection->getParameterList() as $parameter) {
				if (--$grab >= 0 || $parameter->isOptional()) {
					continue;
				}
				$dependencyList[] = $container->factory($container->getFactory($class = $parameter->getClass(), $name), $class, [], $name);
			}
			return array_merge($parameterList, $dependencyList);
		}
	}
