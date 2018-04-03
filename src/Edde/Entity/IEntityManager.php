<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Container\ContainerException;
	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;
	use stdClass;

	interface IEntityManager {
		/**
		 * just create an entity with the given schema
		 *
		 * @param ISchema       $schema
		 * @param stdClass|null $default
		 *
		 * @return IEntity
		 *
		 * @throws ContainerException
		 * @throws SchemaException
		 */
		public function entity(ISchema $schema, stdClass $default = null): IEntity;
	}
