<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\IProperty;
	use Edde\Api\Entity\Exception\UnknownAliasException;
	use Edde\Api\Entity\Query\IDetachQuery;
	use Edde\Api\Entity\Query\IDisconnectQuery;
	use Edde\Api\Schema\Exception\InvalidRelationException;
	use Edde\Api\Schema\Exception\LinkException;
	use Edde\Api\Schema\Exception\RelationException;
	use Edde\Api\Schema\Exception\SchemaException;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\Exception\DuplicateEntryException;
	use Edde\Api\Validator\Exception\BatchValidationException;
	use Edde\Api\Validator\Exception\ValidationException;

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
		 * be for example uuid, ...); shortcut for primary->get()
		 *
		 * @return string
		 */
		public function getHash(): string;

		/**
		 * make a link from $this to the $entity (basically 1:N relation)
		 *
		 * @param IEntity $entity
		 *
		 * @return IEntity $this
		 */
		public function linkTo(IEntity $entity): IEntity;

		/**
		 * load 1:n link (kind of foreign key)
		 *
		 * @param string $schema
		 *
		 * @return IEntity
		 * @throws LinkException
		 */
		public function link(string $schema): IEntity;

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
		 * @param IEntity     $entity
		 * @param string|null $relation
		 *
		 * @return IEntity
		 * @throws RelationException
		 */
		public function attach(IEntity $entity, string $relation = null): IEntity;

		/**
		 * detach all relations to the given entity (entity will be detached on save)
		 *
		 * @param IEntity     $entity
		 * @param string|null $relation
		 *
		 * @return IDetachQuery
		 */
		public function detach(IEntity $entity, string $relation = null): IDetachQuery;

		/**
		 * detach all relations to the given schema
		 *
		 * @param string $schema
		 *
		 * @return IDisconnectQuery
		 * @throws InvalidRelationException
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
		 * @throws SchemaException
		 * @throws InvalidRelationException
		 * @throws UnknownAliasException
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
		 * @throws ValidationException
		 * @throws BatchValidationException
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
		 * @throws ValidationException
		 */
		public function validate(): IEntity;
	}
