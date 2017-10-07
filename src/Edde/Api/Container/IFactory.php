<?php
	declare(strict_types=1);
	namespace Edde\Api\Container;

	/**
	 * Factory is general way how to build a dependency with the final set of parameters/dependencies.
	 */
	interface IFactory {
		/**
		 * is this factory able to handle the given input?
		 *
		 * @param IContainer $container
		 * @param string     $dependency
		 *
		 * @return bool
		 */
		public function canHandle(IContainer $container, string $dependency): bool;

		/**
		 * create a reflection for the given dependency
		 *
		 * @param IContainer $container
		 * @param string     $dependency
		 *
		 * @return IReflection
		 */
		public function getReflection(IContainer $container, string $dependency): IReflection;

		/**
		 * 90% usecase is to return self, but in some rare cases factory can return another factory
		 *
		 * @param IContainer $container
		 *
		 * @return IFactory
		 */
		public function getFactory(IContainer $container): IFactory;

		/**
		 * try to prefetch dependency before heavy computations are done
		 *
		 * @param IContainer $container
		 * @param string     $name
		 * @param array      $parameterList
		 *
		 * @return mixed|null if null is returned, container should execute dependency creation
		 */
		public function fetch(IContainer $container, string $name, array $parameterList);

		/**
		 * @param IContainer  $container
		 * @param array       $parameterList
		 * @param IReflection $dependency
		 * @param string      $name
		 *
		 * @return mixed
		 */
		public function factory(IContainer $container, array $parameterList, IReflection $dependency, string $name = null);

		/**
		 * factory can optionally push dependency to some kind of cache (this instance should be returned on fetch())
		 *
		 * @param IContainer $container
		 * @param mixed      $instance
		 *
		 * @return mixed
		 */
		public function push(IContainer $container, $instance);
	}
