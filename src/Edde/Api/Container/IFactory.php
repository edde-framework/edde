<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	use Edde\Api\Callback\IParameter;

	/**
	 * Interface for variaus factory implementations for a dependency container.
	 */
	interface IFactory {
		/**
		 * get name of this factory; this should usually by name of interface
		 *
		 * @param string $name
		 *
		 * @return string
		 */
		public function getName(string $name = null): string;

		/**
		 * switch singleton flag of this factory; should be used only in the configuration time
		 *
		 * @param bool $singleton
		 *
		 * @return IFactory
		 */
		public function setSingleton(bool $singleton): IFactory;

		/**
		 * is result of this factory singleton?
		 *
		 * @return bool
		 */
		public function isSingleton(): bool;

		/**
		 * return list of required parameters for this factory
		 *
		 * @param string $name
		 *
		 * @return IParameter[]
		 */
		public function getParameterList(string $name = null): array;

		/**
		 * callback of this method should be called when instance of this factory is created
		 *
		 * @param callable $callback
		 *
		 * @return IFactory
		 */
		public function deffered(callable $callback): IFactory;

		/**
		 * is this factory able to handle the given identifier?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function canHandle(string $name): bool;

		/**
		 * create instance from this factory; container should used for injecting dependencies
		 *
		 * @param string $name
		 * @param array $parameterList
		 * @param IContainer $container
		 *
		 * @return mixed
		 */
		public function create(string $name, array $parameterList, IContainer $container);
	}
