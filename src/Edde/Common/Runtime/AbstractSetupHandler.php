<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Runtime\ISetupHandler;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Event\EventBus;

	/**
	 * Common class for all setup handlers.
	 */
	abstract class AbstractSetupHandler extends EventBus implements ISetupHandler {
		/**
		 * @var IFactory[]
		 */
		protected $factoryList = [];

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function registerFactoryList(array $fatoryList): ISetupHandler {
			$this->factoryList = FactoryFactory::createList(array_merge($this->factoryList, $fatoryList));
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws RuntimeException
		 */
		public function deffered(string $name, callable $onSetup): ISetupHandler {
			if (isset($this->factoryList[$name]) === false) {
				throw new RuntimeException(sprintf('Cannot use deffered setup on unknown cache [%s].', $name));
			}
			$this->factoryList[$name]->deffered(function (IContainer $container, $instance) use ($onSetup) {
				if (($instance instanceof IDeffered) === false) {
					throw new RuntimeException(sprintf('Deffered class must implement [%s] interface.', IDeffered::class));
				}
				/** @var $instance IDeffered */
				$instance->onDeffered(function () use ($container, $onSetup, $instance) {
					return $container->call($onSetup, $instance);
				});
			});
			return $this;
		}
	}
