<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Connection\ConnectionException;
	use Edde\Entity\IEntity;
	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;
	use Edde\Schema\SchemaValidationException;
	use Edde\Validator\ValidatorException;
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
		 * @throws CollectionException
		 */
		public function create(): ICollection;

		/**
		 * save the given source in a collection; alias makes use of a schema internally
		 *
		 * @param string   $alias
		 * @param stdClass $source
		 *
		 * @return IEntity return newly create entity
		 *
		 * @throws CollectionException
		 * @throws ValidatorException
		 * @throws SchemaValidationException
		 * @throws ConnectionException
		 */
		public function save(string $alias, stdClass $source): IEntity;

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
