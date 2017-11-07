<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		use Edde\Api\Crate\ICrate;
		use Edde\Api\Crate\IProperty;
		use Edde\Api\Schema\ISchema;

		/**
		 * An Entity is extended Crate with some additional features.
		 */
		interface IEntity extends ICrate {
			/**
			 * get entity's schema
			 *
			 * @return ISchema
			 */
			public function getSchema(): ISchema;

			/**
			 * return primary property of this entity
			 *
			 * @return IProperty
			 */
			public function getPrimary(): IProperty;

			/**
			 * load the given data (they should be also filtered)
			 *
			 * @param array $source
			 *
			 * @return IEntity
			 */
			public function filter(array $source): IEntity;

			/**
			 * make a link from $this to the $entity (basically 1:N relation)
			 *
			 * @param IEntity $entity
			 *
			 * @return IEntity $this
			 */
			public function linkTo(IEntity $entity): IEntity;

			/**
			 * get an entity by the given schema
			 *
			 * @param string $schema
			 *
			 * @return ICollection
			 */
			public function link(string $schema): ICollection;

			/**
			 * prepare m:n collection of related entities
			 *
			 * @param string $schema
			 * @param string $alias
			 *
			 * @return ICollection
			 */
			public function join(string $schema, string $alias): ICollection;
		}
