<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\IProperty;
	use Edde\Api\Entity\Query\IDetachQuery;
	use Edde\Api\Entity\Query\IDisconnectQuery;
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
		 * @param IEntity $entity
		 *
		 * @return IEntity
		 */
		public function attach(IEntity $entity): IEntity;

		/**
		 * detach all relations to the given entity (entity will be detached on save)
		 *
		 * @param IEntity $entity
		 *
		 * @return IDetachQuery
		 */
		public function detach(IEntity $entity): IDetachQuery;

		/**
		 * detach all relations to the given schema
		 *
		 * @param string $schema
		 *
		 * @return IDisconnectQuery
		 */
		public function disconnect(string $schema): IDisconnectQuery;

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