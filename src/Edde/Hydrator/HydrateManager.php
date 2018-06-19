<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	use Edde\Edde;
	use Edde\Service\Container\Container;
	use Edde\Service\Storage\Storage;
	use Generator;

	class HydrateManager extends Edde implements IHydrateManager {
		use Container;
		use Storage;
		/**
		 * @var SchemaHydrator
		 */
		protected $schemaHydrator;

		/** @inheritdoc */
		public function value(string $query, array $params = []): Generator {
			return $this->storage->hydrate($query, new ValueHydrator(), $params);
		}

		/** @inheritdoc */
		public function schema(string $name, string $query, array $params = []): Generator {
			return $this->storage->hydrate($query, $this->schemaHydrator ?: $this->container->inject($this->schemaHydrator = new SimpleHydrator($name)), $params);
		}
	}
