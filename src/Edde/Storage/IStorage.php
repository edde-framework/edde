<?php
declare(strict_types=1);

namespace Edde\Storage;

use Edde\Hydrator\IHydrator;
use Edde\Schema\SchemaException;
use Edde\Transaction\ITransaction;
use Edde\Transaction\TransactionException;
use Generator;
use Throwable;

/**
 * Low-level storage implementation with all supported query types explicitly typed.
 */
interface IStorage extends ITransaction {
    /**
     * execute raw query which should return some data
     *
     * @param mixed $query
     * @param array $params
     *
     * @return mixed
     *
     * @throws StorageException
     * @throws UnknownTableException
     */
    public function fetch(string $query, array $params = []);

    /**
     * exec raw query without returning any data (create database, table, ...)
     *
     * @param mixed $query
     * @param array $params
     *
     * @return mixed
     *
     * @throws StorageException
     * @throws UnknownTableException
     */
    public function exec(string $query, array $params = []);

    /**
     * create schema with the given name
     *
     * @param string $name
     *
     * @return IStorage
     *
     * @throws StorageException
     */
    public function create(string $name): IStorage;

    /**
     * create all the given schemas (out of transaction)
     *
     * @param array $names
     *
     * @return IStorage
     *
     * @throws StorageException
     */
    public function creates(array $names): IStorage;

    /**
     * hydrate output with the given hydrator
     *
     * @param string    $query
     * @param IHydrator $hydrator
     * @param array     $params
     *
     * @return Generator generator or proprietary hydrator value
     *
     * @throws StorageException
     * @throws UnknownTableException
     */
    public function hydrate(string $query, IHydrator $hydrator, array $params = []): Generator;

    /**
     * hydrate a single entity from the given query; there is no cursor used
     *
     * @param string $name
     * @param string $query
     * @param array  $params
     *
     * @return IEntity
     *
     * @throws EmptyEntityException
     * @throws StorageException
     */
    public function single(string $name, string $query, array $params = []): IEntity;

    /**
     * hydrate a single value from the query
     *
     * @param string $query
     * @param array  $params
     *
     * @return Generator|mixed
     *
     * @throws StorageException
     * @throws UnknownTableException
     */
    public function value(string $query, array $params = []): Generator;

    /**
     * hydrate the given schema from a query
     *
     * @param string $name
     * @param string $query
     * @param array  $params
     *
     * @return Generator|IEntity[]
     *
     * @throws StorageException
     * @throws UnknownTableException
     */
    public function schema(string $name, string $query, array $params = []): Generator;

    /**
     * assisted (general) query
     *
     * @param string $query
     * @param array  $schemas
     *
     * @return string
     *
     * @throws StorageException
     */
    public function query(string $query, array $schemas = []): string;

    /**
     * insert a new item into storage ($name is schema name)
     *
     * @param IEntity        $entity
     * @param IHydrator|null $hydrator
     *
     * @return IEntity
     *
     * @throws StorageException
     */
    public function insert(IEntity $entity, IHydrator $hydrator = null): IEntity;

    /**
     * insert multiple entities (out of transaction)
     *
     * @param iterable|IEntity[] $inserts
     * @param IHydrator|null     $hydrator
     *
     * @return IStorage
     *
     * @throws StorageException
     */
    public function inserts(iterable $inserts, IHydrator $hydrator = null): IStorage;

    /**
     * @param IEntity        $entity
     * @param IHydrator|null $hydrator
     *
     * @return IEntity
     *
     * @throws StorageException
     */
    public function update(IEntity $entity, IHydrator $hydrator = null): IEntity;

    /**
     * save the given item (upsert)
     *
     * @param IEntity        $entity
     * @param IHydrator|null $hydrator
     *
     * @return IEntity
     *
     * @throws StorageException
     */
    public function save(IEntity $entity, IHydrator $hydrator = null): IEntity;

    /**
     * create a new relation
     *
     * @param IEntity $source   entity could contain just unique value
     * @param IEntity $target   entity could contain just unique value
     * @param string  $relation name of relation schema
     *
     * @return IEntity relation (not saved yet)
     *
     * @throws StorageException
     * @throws SchemaException
     */
    public function attach(IEntity $source, IEntity $target, string $relation): IEntity;

    /**
     * create a new 1:n relation
     *
     * @param IEntity $source   entity could contain just unique value
     * @param IEntity $target   entity could contain just unique value
     * @param string  $relation name of relation schema
     *
     * @return IEntity relation (not saved yet)
     *
     * @throws StorageException
     * @throws SchemaException
     */
    public function link(IEntity $source, IEntity $target, string $relation): IEntity;

    /**
     * remove all relations between the given schemas
     *
     * @param IEntity $source   entity could contain just unique value
     * @param IEntity $target   entity could contain just unique value
     * @param string  $relation name of relation schema
     *
     * @return IStorage
     *
     * @throws StorageException
     * @throws SchemaException
     */
    public function unlink(IEntity $source, IEntity $target, string $relation): IStorage;

    /**
     * load exactly one item or throw an exception
     *
     * @param string $name
     * @param string $uuid
     *
     * @return IEntity
     *
     * @throws StorageException
     * @throws EmptyEntityException
     */
    public function load(string $name, string $uuid): IEntity;

    /**
     * enable temporal context; callback will get temporary table nam
     *
     * @param string   $type
     * @param iterable $items
     * @param callable $callback
     *
     * @return iterable
     *
     * @throws StorageException
     * @throws TransactionException
     */
    public function temporal(string $type, iterable $items, callable $callback): iterable;

    /**
     * @param IEntity $entity
     *
     * @return IStorage
     *
     * @throws StorageException
     * @throws SchemaException
     */
    public function delete(IEntity $entity): IStorage;

    /**
     * @param string $string
     *
     * @return string
     */
    public function delimit(string $string): string;

    /**
     * translate input exception to concrete storage exception
     *
     * @param Throwable $throwable
     *
     * @return Throwable
     */
    public function exception(Throwable $throwable): Throwable;
}
