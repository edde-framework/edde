<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		use Edde\Api\Crate\ICrate;
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
			 * load the given data (they should be also filtered)
			 *
			 * @param array $source
			 *
			 * @return IEntity
			 */
			public function load(array $source): IEntity;

			/**
			 * is the entity loaded from storage, thus it exists?
			 *
			 * @return bool
			 */
			public function exists(): bool;
		}
