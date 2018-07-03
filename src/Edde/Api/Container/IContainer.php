<?php
	declare(strict_types=1);

	namespace Edde\Api\Container;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Config\IConfigurator;
	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\Exception\UnknownFactoryException;

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
		 * @param array $factoryList
		 *
		 * @return IContainer
		 */
		public function registerFactoryList(array $factoryList): IContainer;

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
		 * @param IConfigurator[] $configuratorList
		 *
		 * @return IContainer
		 */
		public function registerConfiguratorList(array $configuratorList): IContainer;

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
		 * @throws UnknownFactoryException
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
		 * execute given callback with autowired dependencies
		 *
		 * @param callable $callable
		 * @param array    $parameterList
		 * @param string   $source
		 *
		 * @return mixed
		 */
		public function call(callable $callable, array $parameterList = [], string $source = null);

		/**
		 * general method for dependency creation (so call and create should call this one)
		 *
		 * @param IFactory    $factory
		 * @param array       $parameterList
		 * @param string|null $name
		 * @param string      $source
		 *
		 * @return mixed
		 */
		public function factory(IFactory $factory, array $parameterList = [], string $name = null, string $source = null);

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
		 * @param IDependency $dependency
		 * @param bool        $lazy
		 *
		 * @return mixed
		 */
		public function dependency($instance, IDependency $dependency, bool $lazy = true);
	}
