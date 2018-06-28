<?php
	declare(strict_types=1);
	namespace Edde\Factory;

	use Edde\Container\ContainerException;
	use Edde\Container\IContainer;
	use Edde\Edde;

	/**
	 * Basic implementation for all dependency factories.
	 */
	abstract class AbstractFactory extends Edde implements IFactory {
		/** @inheritdoc */
		public function getUuid(): ?string {
			return null;
		}

		/** @inheritdoc */
		public function getFactory(IContainer $container): IFactory {
			return $this;
		}

		/** @inheritdoc */
		public function fetch(IContainer $container, string $name, array $params) {
		}

		/** @inheritdoc */
		public function push(IContainer $container, $instance) {
			return $instance;
		}

		/**
		 * @param IContainer  $container
		 * @param array       $params
		 * @param IReflection $reflection
		 * @param string|null $name
		 *
		 * @return array
		 *
		 * @throws ContainerException
		 */
		protected function params(IContainer $container, array $params, IReflection $reflection, string $name = null) {
			$grab = count($params);
			$dependencyList = [];
			foreach ($reflection->getParams() as $parameter) {
				if (--$grab >= 0 || $parameter->isOptional()) {
					continue;
				}
				$dependencyList[] = $container->factory($container->getFactory($class = $parameter->getClass(), $name), $class, [], $name);
			}
			return array_merge($params, $dependencyList);
		}
	}
