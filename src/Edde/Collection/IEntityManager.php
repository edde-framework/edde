<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Schema\SchemaException;

	interface IEntityManager {
		/**
		 * just create an entity with the given schema
		 *
		 * @param string     $schema
		 * @param array|null $default
		 *
		 * @return IEntity
		 *
		 * @throws SchemaException
		 */
		public function entity(string $schema, array $default = null): IEntity;
	}
