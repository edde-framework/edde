<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Schema\SchemaException;
	use stdClass;

	interface IEntityManager {
		/**
		 * just create an entity with the given schema
		 *
		 * @param string        $schema
		 * @param stdClass|null $default
		 *
		 * @return IEntity
		 *
		 * @throws SchemaException
		 */
		public function entity(string $schema, stdClass $default = null): IEntity;
	}
