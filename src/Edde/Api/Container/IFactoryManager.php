<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	/**
	 * General purpose cache management; used byt an Container and a DependencyFactory.
	 */
	interface IFactoryManager {
		/**
		 * register given cache
		 *
		 * @param string $name
		 * @param IFactory $factory
		 *
		 * @return IFactoryManager
		 */
		public function registerFactory(string $name, IFactory $factory): IFactoryManager;

		/**
		 * @param IFactory[] $factoryList
		 *
		 * @return IFactoryManager
		 */
		public function registerFactoryList(array $factoryList): IFactoryManager;

		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasFactory(string $name): bool;

		/**
		 * @param string $name
		 *
		 * @return IFactory
		 *
		 * @throws FactoryException
		 */
		public function getFactory(string $name): IFactory;

		/**
		 * return registered set of factories
		 *
		 * @return array
		 */
		public function getFactoryList(): array;
	}
