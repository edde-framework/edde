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
			 * unique representation of this entity (including persistence, this could
			 * be for example guid, ...); shortcut for primary->get()
			 *
			 * @return string
			 */
			public function getHash(): string;

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
			 * unlink the given schema (unset 1:N relation)
			 *
			 * @param string $schema
			 *
			 * @return IEntity
			 */
			public function unlink(string $schema): IEntity;

			/**
			 * attach an entity and return a relation entity (returned entity !== $this)
			 *
			 * @param IEntity $entity
			 *
			 * @return IEntity
			 */
			public function attach(IEntity $entity): IEntity;

			/**
			 * prepare m:n collection of related entities
			 *
			 * @param string $schema
			 * @param string $alias
			 *
			 * @return ICollection
			 */
			public function join(string $schema, string $alias): ICollection;

			/**
			 * save this entity and all related entities (entity queue in a transaction)
			 *
			 * @return IEntity
			 */
			public function save(): IEntity;

			/**
			 * return sanitized array of this entity
			 *
			 * @return array
			 */
			public function sanitize(): array;
		}
