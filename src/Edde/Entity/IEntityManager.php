<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Config\IConfigurable;
	use Edde\Schema\ISchema;

	interface IEntityManager extends IConfigurable {
		/**
		 * just create an entity with the given schema
		 *
		 * @param ISchema $schema
		 *
		 * @return IEntity
		 *
		 * @throws EntityException
		 */
		public function entity(ISchema $schema): IEntity;
	}
