<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Crate\ICrate;
	use Edde\Crate\IProperty;
	use Edde\Driver\DuplicateEntryException;
	use Edde\Query\IDetachQuery;
	use Edde\Query\IDisconnectQuery;
	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;

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
		 * @return \Edde\Crate\IProperty
		 *
		 * @throws SchemaException
		 */
		public function getPrimary(): IProperty;

		/**
		 * unique representation of this entity (including persistence, this could
		 * be for example uuid, ...); shortcut for primary->get()
		 *
		 * @return string
		 *
		 * @throws SchemaException
		 */
		public function getHash(): string;

		/**
		 * make a link from $this to the $entity (basically 1:N relation)
		 *
		 * @param IEntity $entity
		 *
		 * @return IEntity $this
		 *
		 * @throws SchemaException
		 */
		public function linkTo(IEntity $entity): IEntity;

		/**
		 * load 1:n link (kind of foreign key)
		 *
		 * @param string $schema
		 *
		 * @return IEntity
		 *
		 * @throws SchemaException
		 */
		public function link(string $schema): IEntity;

		/**
		 * unlink the given schema (unset 1:N relation)
		 *
		 * @param string $schema
		 *
		 * @return IEntity
		 *
		 * @throws SchemaException
		 */
		public function unlink(string $schema): IEntity;

		/**
		 * attach an entity and return a relation entity (returned entity !== $this)
		 *
		 * @param IEntity     $entity
		 * @param string|null $relation
		 *
		 * @return IEntity
		 *
		 * @throws SchemaException
		 */
		public function attach(IEntity $entity, string $relation = null): IEntity;

		/**
		 * detach all relations to the given entity (entity will be detached on save)
		 *
		 * @param IEntity     $entity
		 * @param string|null $relation
		 *
		 * @return IDetachQuery
		 *
		 * @throws SchemaException
		 */
		public function detach(IEntity $entity, string $relation = null): IDetachQuery;

		/**
		 * detach all relations to the given schema
		 *
		 * @param string $schema
		 *
		 * @return IDisconnectQuery
		 *
		 * @throws SchemaException
		 */
		public function disconnect(string $schema): IDisconnectQuery;

		/**
		 * prepare m:n collection of related entities
		 *
		 * @param string      $alias
		 * @param string      $schema
		 * @param string|null $relation if there are more relation schemas, which one should be used
		 *
		 * @return ICollection
		 */
		public function join(string $alias, string $schema, string $relation = null): ICollection;

		/**
		 * @param string      $alias
		 * @param string      $schema
		 * @param string|null $relation
		 *
		 * @return ICollection
		 */
		public function reverseJoin(string $alias, string $schema, string $relation = null): ICollection;

		/**
		 * mark this entity for delete (it's not deleted until save)
		 *
		 * @return IEntity
		 */
		public function delete(): IEntity;

		/**
		 * save this entity and all related entities (entity queue in a transaction)
		 *
		 * @return IEntity
		 *
		 * @throws DuplicateEntryException
		 */
		public function save(): IEntity;

		/**
		 * load the given data (they should be also filtered)
		 *
		 * @param array $source
		 *
		 * @return IEntity
		 */
		public function filter(array $source): IEntity;

		/**
		 * return sanitized array of this entity
		 *
		 * @return array
		 */
		public function sanitize(): array;

		/**
		 * is this entity valid?
		 *
		 * @return bool
		 */
		public function isValid(): bool;

		/**
		 * validate data of an entity
		 *
		 * @return IEntity
		 */
		public function validate(): IEntity;
	}
