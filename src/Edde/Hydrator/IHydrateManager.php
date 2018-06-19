<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	use Edde\Config\IConfigurable;
	use Edde\Storage\StorageException;
	use Generator;

	interface IHydrateManager extends IConfigurable {
		/**
		 * hydrate a single value from the query
		 *
		 * @param string $query
		 * @param array  $params
		 *
		 * @return Generator|mixed
		 *
		 * @throws StorageException
		 */
		public function value(string $query, array $params = []): Generator;

		/**
		 * hydrate the given schema from a query
		 *
		 * @param string $name
		 * @param string $query
		 * @param array  $params
		 *
		 * @return Generator|array
		 *
		 * @throws StorageException
		 */
		public function schema(string $name, string $query, array $params = []): Generator;
	}
