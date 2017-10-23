<?php
	namespace Edde\Api\Storage;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Api\Storage\Exception\StorageException;

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
			 * save the given entity; the storage should check, if an entity is already present and
			 * do proper inert/update
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
			 * get a collection of entities (collection should use generator or iterator, never fetch to array)
			 *
			 * @param IQuery $query
			 *
			 * @return ICollection|IEntity[]
			 */
			public function collection(IQuery $query): ICollection;

			/**
			 * load exactly one entity or throw an exception if the entity is not found
			 *
			 * @param IQuery $query
			 *
			 * @return IEntity
			 *
			 * @throws EntityNotFoundException
			 */
			public function load(IQuery $query): IEntity;
		}
