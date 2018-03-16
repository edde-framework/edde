<?php
	declare(strict_types=1);
	namespace Edde\Common\Container\Factory;

	use Edde\Container\IContainer;
	use Edde\Container\IReflection;

	/**
	 * When there is need to search for a class in namespace hierarchy.
	 */
	abstract class AbstractDiscoveryFactory extends ClassFactory {
		/**
		 * @var string[]
		 */
		protected $names = [];

		/**
		 * @inheritdoc
		 */
		public function canHandle(IContainer $container, string $dependency): bool {
			if ($discover = $this->search($dependency)) {
				return parent::canHandle($container, $discover);
			}
			return false;
		}

		/**
		 * @inheritdoc
		 */
		public function getReflection(IContainer $container, string $dependency): IReflection {
			return parent::getReflection($container, $this->search($dependency));
		}

		/**
		 * @inheritdoc
		 */
		public function factory(IContainer $container, array $params, IReflection $dependency, string $name = null) {
			return parent::factory($container, $params, $dependency, $this->search($name));
		}

		protected function search(string $name) {
			if (isset($this->names[$name]) || array_key_exists($name, $this->names)) {
				return $this->names[$name];
			}
			/** @noinspection ForeachSourceInspection */
			foreach ($this->discover($name) as $source) {
				if (class_exists($source)) {
					return $this->names[$name] = $source;
				}
			}
			return $this->names[$name] = null;
		}

		/**
		 * this method should return set of class names where this factory should look into
		 *
		 * @param string $name
		 *
		 * @return string[]
		 */
		abstract protected function discover(string $name): array;
	}
