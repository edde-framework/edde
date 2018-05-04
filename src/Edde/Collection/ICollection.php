<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Container\ContainerException;
	use Edde\Schema\SchemaException;
	use Edde\Storage\StorageException;
	use IteratorAggregate;
	use stdClass;
	use Traversable;

	/**
	 * A collection is read-only result of some (usually selection) query.
	 */
	interface ICollection extends IteratorAggregate {
		/**
		 * create all schemas in this collection (simply, CREATE TABLE ...)
		 *
		 * thus should run in exclusive transaction as some database systems has
		 * problems with schema & data modifications in one transaction
		 *
		 * @return ICollection
		 *
		 * @throws StorageException
		 */
		public function create(): ICollection;

		/**
		 * insert a new item into a schema represented by the given alias
		 *
		 * @param string   $alias
		 * @param stdClass $source
		 *
		 * @return IEntity return newly create entity
		 *
		 * @throws CollectionException
		 * @throws StorageException
		 * @throws EntityException
		 * @throws SchemaException
		 * @throws ContainerException
		 */
		public function insert(string $alias, stdClass $source): IEntity;

		/**
		 * @return Traversable|IRecord[]
		 */
		public function getIterator();
	}
