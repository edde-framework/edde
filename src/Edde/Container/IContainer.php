<?php
	declare(strict_types=1);
	namespace Edde\Container;

	use Edde\Config\IConfigurable;
	use Edde\Config\IConfigurator;

	/**
	 * Implementation of Dependency Injection Container.
	 */
	interface IContainer extends IConfigurable {
		/**
		 * @param IFactory $factory
		 *
		 * @return IContainer
		 */
		public function registerFactory(IFactory $factory): IContainer;

		/**
		 * shorthand for cache registration
		 *
		 * @param array $factories
		 *
		 * @return IContainer
		 */
		public function registerFactories(array $factories): IContainer;

		/**
		 * register a new config handler for the given dependency
		 *
		 * @param string                     $name
		 * @param \Edde\Config\IConfigurator $configurator
		 *
		 * @return IContainer
		 */
		public function registerConfigurator(string $name, IConfigurator $configurator): IContainer;

		/**
		 * register list of config handlers bound to the given factories (key is factory name, value is config handler)
		 *
		 * @param IConfigurator[] $configurators
		 *
		 * @return IContainer
		 */
		public function registerConfigurators(array $configurators): IContainer;

		/**
		 * do container have a factory for the given dependency? - only check if dependency is available, but
		 * don't ensure that it's possible to create it
		 *
		 * @param string $dependency
		 *
		 * @return bool
		 */
		public function canHandle(string $dependency): bool;

		/**
		 * get factory which is able to create the given dependency
		 *
		 * @param mixed  $dependency
		 * @param string $source
		 *
		 * @return IFactory
		 *
		 * @throws ContainerException
		 */
		public function getFactory(string $dependency, string $source = null): IFactory;

		/**
		 * create the dependency by it's identifier (name)
		 *
		 * @param string $name
		 * @param array  $params
		 * @param string $source who has requested this dependency
		 *
		 * @return mixed
		 *
		 * @throws ContainerException
		 */
		public function create(string $name, array $params = [], string $source = null);

		/**
		 * general method for dependency creation (so call and create should call this one)
		 *
		 * @param IFactory $factory
		 * @param string   $name
		 * @param array    $params
		 * @param string   $source
		 *
		 * @return mixed
		 *
		 * @throws ContainerException
		 */
		public function factory(IFactory $factory, string $name, array $params = [], string $source = null);

		/**
		 * try to autowire dependencies to $instance
		 *
		 * @param mixed $instance
		 *
		 * @return mixed
		 *
		 * @throws ContainerException
		 */
		public function inject($instance);

		/**
		 * execute injects on the given instance
		 *
		 * @param mixed       $instance
		 * @param IReflection $reflection
		 *
		 * @return mixed
		 *
		 * @throws ContainerException
		 */
		public function dependency($instance, IReflection $reflection);
	}
