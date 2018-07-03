<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	use Edde\Api\Deffered\IDeffered;

	/**
	 * Implementation of Dependency Inject Container.
	 */
	interface IContainer extends IDeffered {
		/**
		 * shorthand for cache registration
		 *
		 * @param string $name
		 * @param IFactory $factory
		 *
		 * @return IContainer
		 */
		public function registerFactory(string $name, IFactory $factory): IContainer;

		/**
		 * shorthand for cache registration
		 *
		 * @param array $factoryList
		 *
		 * @return IContainer
		 */
		public function registerFactoryList(array $factoryList): IContainer;

		/**
		 * check if the given name is available (known) in a container
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function has(string $name);

		/**
		 * create the dependency by it's identifier (name)
		 *
		 * @param string $name
		 * @param array ...$parameterList
		 *
		 * @return mixed
		 */
		public function create(string $name, ...$parameterList);

		/**
		 * execute given callback with autowired dependencies
		 *
		 * @param callable $callable
		 * @param array $parameterList
		 *
		 * @return mixed
		 */
		public function call(callable $callable, ...$parameterList);

		/**
		 * low-level method for factory execution (other container methods should be using this)
		 *
		 * @param IFactory $factory
		 * @param string $name optional dependency name (name given from outside)
		 * @param array $parameterList
		 *
		 * @return mixed return created instance of the given factory (result of factory execution)
		 */
		public function factory(IFactory $factory, string $name = null, array $parameterList = []);

		/**
		 * provides all aditional dependencies for the given instance
		 *
		 * @param mixed $instance
		 *
		 * @return mixed return input instance (input is same as output)
		 */
		public function inject($instance);
	}
