<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Config\IConfigurable;
	use stdClass;

	interface IEntityManager extends IConfigurable {
		/**
		 * just create an entity with the given schema
		 *
		 * @param string $schema
		 *
		 * @return IEntity
		 *
		 * @throws EntityException
		 */
		public function create(string $schema): IEntity;

		/**
		 * shorthand for put & save
		 *
		 * @param string   $schema
		 * @param stdClass $source
		 *
		 * @return IEntity
		 *
		 * @throws EntityException
		 */
		public function save(string $schema, stdClass $source): IEntity;
	}
