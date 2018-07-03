<?php
	declare(strict_types = 1);

	namespace Edde\Api\Runtime;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Event\IEventBus;

	/**
	 * Standard interface for basic runtime setup.
	 */
	interface ISetupHandler extends IEventBus {
		/**
		 * @param IFactory[] $fatoryList
		 *
		 * @return ISetupHandler
		 */
		public function registerFactoryList(array $fatoryList): ISetupHandler;

		/**
		 * attach onDeffered handler to a given class/identifier (it must be IDeffered)
		 *
		 * @param string $name
		 * @param callable $onSetup
		 *
		 * @return ISetupHandler
		 */
		public function deffered(string $name, callable $onSetup): ISetupHandler;

		/**
		 * run initial application setup and return system container
		 *
		 * @return IContainer
		 */
		public function createContainer(): IContainer;
	}
