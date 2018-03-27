<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Connection\ConnectionException;
	use Edde\Container\ContainerException;
	use Edde\Entity\EntityException;
	use Edde\Entity\IEntity;
	use Edde\Generator\GeneratorException;
	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;
	use Edde\Transaction\TransactionException;
	use IteratorAggregate;
	use stdClass;
	use Traversable;

	/**
	 * A collection is read-only result of some (usually selection) query.
	 */
	interface ICollection extends IteratorAggregate {
		/**
		 * @param string $schema
		 * @param string $alias
		 *
		 * @return ICollection
		 *
		 * @throws SchemaException
		 */
		public function use(string $schema, string $alias = null): ICollection;

		/**
		 * [$alias => $schema]
		 *
		 * @param array $schemas
		 *
		 * @return ICollection
		 *
		 * @throws SchemaException
		 */
		public function uses(array $schemas): ICollection;

		/**
		 * create all schemas in this collection (simply, CREATE TABLE ...)
		 *
		 * thus should run in exclusive transaction as some database systems has
		 * problems with schema & data modifications in one transaction
		 *
		 * @return ICollection
		 *
		 * @throws TransactionException
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
		 * @throws ConnectionException
		 * @throws EntityException
		 * @throws SchemaException
		 * @throws GeneratorException
		 * @throws ContainerException
		 */
		public function insert(string $alias, stdClass $source): IEntity;

		/**
		 * return schema for the given alias
		 *
		 * @param string $alias
		 *
		 * @return ISchema
		 *
		 * @throws CollectionException
		 */
		public function getSchema(string $alias): ISchema;

		/**
		 * @return Traversable|IRecord[]
		 */
		public function getIterator();
	}
