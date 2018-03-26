<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Config\IConfigurable;

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
		public function entity(string $schema): IEntity;
	}
