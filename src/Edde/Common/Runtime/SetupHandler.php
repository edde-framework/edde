<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Runtime\ISetupHandler;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Ext\Container\ContainerFactory;

	/**
	 * Default setup handler implementation; it has hard bare bones.
	 */
	class SetupHandler extends AbstractSetupHandler {
		/**
		 * @var IContainer
		 */
		protected $container;

		/**
		 * cache method for a new setup handler with defaults
		 *
		 * @param array $factoryList
		 *
		 * @return ISetupHandler
		 * @throws FactoryException
		 */
		static public function create(array $factoryList = []): ISetupHandler {
			$setupHandler = new self();
			$setupHandler->registerFactoryList($factoryList);
			return $setupHandler;
		}

		/**
		 * @inheritdoc
		 * @throws RuntimeException
		 * @throws FactoryException
		 */
		public function createContainer(): IContainer {
			if ($this->container) {
				throw new RuntimeException(sprintf('Cannot run [%s()] multiple times; something is wrong!', __METHOD__));
			}
			$container = ContainerFactory::simple($this->factoryList)
				->create(IContainer::class);
			$container->registerFactoryList($this->factoryList);
			foreach ($this->factoryList as $factory) {
				$container->inject($factory);
			}
			return $this->container = $container;
		}
	}
