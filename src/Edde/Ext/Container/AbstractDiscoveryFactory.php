<?php
	declare(strict_types=1);

	namespace Edde\Ext\Container;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;
	use Edde\Common\Container\Factory\ClassFactory;

	/**
	 * When there is need to search for a class in namespace hierarchy.
	 */
	abstract class AbstractDiscoveryFactory extends ClassFactory {
		/**
		 * @var string[]
		 */
		protected $nameList = [];

		/**
		 * @inheritdoc
		 */
		public function canHandle(IContainer $container, string $dependency): bool {
			if ($discover = $this->discover($dependency)) {
				return parent::canHandle($container, $discover);
			}
			return false;
		}

		/**
		 * @inheritdoc
		 */
		public function createDependency(IContainer $container, string $dependency = null): IDependency {
			return parent::createDependency($container, $this->discover($dependency));
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IContainer $container, array $parameterList, IDependency $dependency, string $name = null) {
			return parent::execute($container, $parameterList, $dependency, $this->discover($name));
		}

		protected function discover(string $name) {
			if (isset($this->nameList[$name]) || array_key_exists($name, $this->nameList)) {
				return $this->nameList[$name];
			}
			/** @noinspection ForeachSourceInspection */
			foreach ($this->discovery($name) as $source) {
				if (class_exists($source)) {
					return $this->nameList[$name] = $source;
				}
			}
			return $this->nameList[$name] = null;
		}

		/**
		 * this method should return set of class names where this factory should look into
		 *
		 * @param string $name
		 *
		 * @return string[]|false
		 */
		abstract protected function discovery(string $name): array;
	}
