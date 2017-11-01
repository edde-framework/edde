<?php
	namespace Edde\Api\Entity;

		use Edde\Api\Crate\ICrate;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Api\Storage\Exception\StorageException;

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
			 * link the given entity to the current one
			 *
			 * @param IEntity    $entity
			 * @param ILink|null $link
			 *
			 * @return IEntity
			 */
			public function link(IEntity $entity, ILink $link = null): IEntity;

			/**
			 * attach the given entity to $this one using m:n relation; returned entity is the
			 * relation entity
			 *
			 * @param IEntity $entity
			 *
			 * @return IEntity
			 */
			public function attach(IEntity $entity): IEntity;

			/**
			 * save this entity into storage (and all related stuff to this entity)
			 *
			 * @return IEntity
			 *
			 * @throws StorageException
			 * @throws DuplicateEntryException
			 * @throws IntegrityException
			 */
			public function save(): IEntity;

			/**
			 * return collection of this entity; collection is not related to entity itself
			 *
			 * @return ICollection
			 */
			public function collection(): ICollection;

			/**
			 * return current "status" query for this entity; it could generate "insert/update" query,
			 * or "delete" if this entity should be deleted
			 *
			 * this is a trick how to offload quite ugly piece of work from storage outside
			 *
			 * @return IQuery
			 */
			public function getQuery(): IQuery;

			/**
			 * mark this entity as lazy; when it got first request for data, it will be loaded (get, ...)
			 *
			 * @return IEntity
			 */
			public function deffered(): IEntity;
		}
