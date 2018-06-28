<?php
	declare(strict_types=1);
	namespace Edde\Container\Factory;

	use Edde\Container\IContainer;
	use Edde\Container\IReflection;

	class CascadeFactory extends ClassFactory {
		/** @var string[] */
		protected $cascades = [];
		/** @var string[] */
		protected $names = [];

		/**
		 * @param string[] $cascades
		 */
		public function __construct(array $cascades = ['Edde']) {
			$this->cascades = $cascades;
		}

		/** @inheritdoc */
		public function canHandle(IContainer $container, string $dependency): bool {
			if ($name = $this->search($dependency)) {
				return parent::canHandle($container, $name);
			}
			return false;
		}

		/** @inheritdoc */
		public function getReflection(IContainer $container, string $dependency): IReflection {
			return parent::getReflection($container, $this->search($dependency));
		}

		/** @inheritdoc */
		public function factory(IContainer $container, array $params, IReflection $dependency, string $name = null) {
			return parent::factory($container, $params, $dependency, $this->search($name));
		}

		/** @inheritdoc */
		protected function search(string $name = null) {
			if (isset($this->names[$name]) || array_key_exists($name, $this->names)) {
				return $this->names[$name];
			}
			foreach ($this->discover($name) as $source) {
				if (class_exists($source)) {
					return $this->names[$name] = $source;
				}
			}
			return $this->names[$name] = null;
		}

		/** @inheritdoc */
		protected function discover(string $name = null): array {
			if ($name === null) {
				return $this->cascades;
			}
			$names = [];
			foreach ($this->cascades as $cascade) {
				$names[] = $cascade . '\\' . $name;
			}
			return $names;
		}
	}
