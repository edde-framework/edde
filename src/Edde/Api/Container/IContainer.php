<?php
	declare(strict_types=1);
	namespace Edde\Api\Container;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Config\IConfigurator;
	use Edde\Exception\Container\ContainerException;
	use Edde\Exception\Container\FactoryException;
	use Edde\Exception\Container\UnknownFactoryException;

	/**
	 * Implementation of Dependency Injection Container.
	 */
	interface IContainer extends IConfigurable {
		/**
		 * @param IFactory $factory
		 * @param string   $id
		 *
		 * @return IContainer
		 */
		public function registerFactory(IFactory $factory, string $id = null): IContainer;

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
		 * @param string        $name
		 * @param IConfigurator $configurator
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
		 * @throws \Edde\Exception\Container\UnknownFactoryException
		 */
		public function getFactory(string $dependency, string $source = null): IFactory;

		/**
		 * create the dependency by it's identifier (name)
		 *
		 * @param string $name
		 * @param array  $parameterList
		 * @param string $source who has requested this dependency
		 *
		 * @return mixed
		 * @throws FactoryException
		 * @throws ContainerException
		 */
		public function create(string $name, array $parameterList = [], string $source = null);

		/**
		 * general method for dependency creation (so call and create should call this one)
		 *
		 * @param IFactory $factory
		 * @param string   $name
		 * @param array    $params
		 * @param string   $source
		 *
		 * @return mixed
		 */
		public function factory(IFactory $factory, string $name, array $params = [], string $source = null);

		/**
		 * try to autowire dependencies to $instance
		 *
		 * @param mixed $instance
		 * @param bool  $force if true, dependencies will be autowired regardless of lazy injects
		 *
		 * @return mixed
		 */
		public function inject($instance, bool $force = false);

		/**
		 * execute injects on the given instance
		 *
		 * @param mixed       $instance
		 * @param IReflection $reflection
		 * @param bool        $lazy
		 *
		 * @return mixed
		 */
		public function dependency($instance, IReflection $reflection, bool $lazy = true);
	}
