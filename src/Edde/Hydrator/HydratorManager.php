<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	use Edde\Edde;
	use Edde\Service\Container\Container;

	class HydratorManager extends Edde implements IHydratorManager {
		use Container;
		/** @var IHydrator[] */
		protected $schemas = [];
		/** @var IHydrator */
		protected $single;

		/** @inheritdoc */
		public function schema(string $name = null): IHydrator {
			if (isset($this->schemas[$name])) {
				return $this->schemas[$name];
			}
			return $this->container->inject($this->schemas[$name] = new SchemaHydrator($name));
		}

		/**
		 * @return IHydrator
		 */
		public function single(): IHydrator {
			return $this->single ?: $this->single = new SingleHydrator();
		}
	}
