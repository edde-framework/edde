<?php
	namespace Edde\Api\Storage;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Storage\Exception\DuplicateTableException;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Api\Storage\Exception\StorageException;
		use Edde\Api\Storage\Exception\UnknownTableException;

		interface IStorage extends IConfigurable {
			/**
			 * execute the given query against a storage; query should be translated into native query and
			 * executed by a native() method
			 *
			 * @param IQuery $query
			 *
			 * @return mixed
			 */
			public function execute(IQuery $query);

			/**
			 * shorthand to execute native query on a storage
			 *
			 * @param string|mixed $query
			 * @param array        $parameterList
			 *
			 * @return mixed
			 */
			public function query($query, array $parameterList = []);

			/**
			 * executes native query on this storage
			 *
			 * @param INativeQuery $nativeQuery
			 *
			 * @return mixed
			 */
			public function native(INativeQuery $nativeQuery);

			/**
			 * start a transaction on the storage
			 *
			 * @param bool $exclusive
			 *
			 * @return IStorage
			 */
			public function start(bool $exclusive = false): IStorage;

			/**
			 * commit a transaction on storage
			 *
			 * @return IStorage
			 */
			public function commit(): IStorage;

			/**
			 * rollback a transaction on storage
			 *
			 * @return IStorage
			 */
			public function rollback(): IStorage;

			/**
			 * save the given entity; the storage should check, if an entity is already present and
			 * do proper inert/update
			 *
			 * This method is quite heavy; for example when there is a lot of new entities
			 * created, insert method could be used instead as optimization
			 *
			 * @param IEntity $entity
			 *
			 * @return IStorage
			 *
			 * @throws IntegrityException
			 * @throws StorageException
			 */
			public function save(IEntity $entity): IStorage;

			/**
			 * when entity status for this storage is known (like it's new), than it's
			 * possible to use this faster method to insert the entity into storage
			 *
			 * @param IEntity $entity
			 *
			 * @return IStorage
			 */
			public function insert(IEntity $entity): IStorage;

			/**
			 * when user know that entity has changed in this storage (thus it already exists in this
			 * storage), this shortcut method could be used
			 *
			 * @param IEntity $entity
			 *
			 * @return IStorage
			 */
			public function update(IEntity $entity): IStorage;

			/**
			 * get a collection of entities (collection should use generator or iterator, never fetch to array)
			 *
			 * @param string $schema
			 * @param IQuery $query
			 *
			 * @return ICollection|IEntity[]
			 *
			 * @throws UnknownTableException
			 */
			public function collection(string $schema, IQuery $query): ICollection;

			/**
			 * load exactly one entity or throw an exception if the entity is not found
			 *
			 * @param string $schema
			 * @param IQuery $query
			 *
			 * @return IEntity
			 *
			 * @throws EntityNotFoundException
			 * @throws UnknownTableException
			 */
			public function load(string $schema, IQuery $query): IEntity;

			/**
			 * try to get an entity by the given primary key (suppose just one key, for more complex
			 * search use full query) and all unique properties (quite magical method)
			 *
			 * I'ts important to use this method wisely, because it could return surprising result - for
			 * example where unique factors are scattered around different columns on different rows.
			 *
			 * @param string $schema
			 * @param mixed  $primary
			 *
			 * @return IEntity
			 *
			 * @throws EntityNotFoundException
			 * @throws UnknownTableException
			 */
//			public function get(string $schema, $primary): IEntity;
			/**
			 * creates a new schema or throw an exception if already exists (or other error)
			 *
			 * @param string $schema
			 *
			 * @return IStorage
			 * @throws DuplicateTableException
			 */
			public function createSchema(string $schema): IStorage;
		}
