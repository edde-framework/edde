<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\Entity;
	use Edde\Collection\EntityNotFoundException;
	use Edde\Collection\IEntity;
	use Edde\Config\ConfigException;
	use Edde\Filter\FilterException;
	use Edde\Query\IQuery;
	use Edde\Schema\SchemaException;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Validator\ValidatorException;
	use Generator;
	use GraphAware\Bolt\Configuration;
	use GraphAware\Bolt\Exception\MessageFailureException;
	use GraphAware\Bolt\GraphDatabase;
	use GraphAware\Bolt\Protocol\SessionInterface;
	use GraphAware\Bolt\Protocol\V1\Transaction;
	use GraphAware\Bolt\Result\Result;
	use GraphAware\Common\Type\MapAccessor;
	use Throwable;
	use function sprintf;
	use function vsprintf;

	class Neo4jStorage extends AbstractStorage {
		use SchemaManager;
		use Container;
		/** @var SessionInterface */
		protected $session;
		/** @var Transaction */
		protected $transaction;

		public function __construct(string $config = 'neo4j') {
			parent::__construct($config);
		}

		/** @inheritdoc */
		public function fetch($query, array $params = []) {
			try {
				return (function (Result $result) {
					foreach ($result->getRecords() as $record) {
						$keys = $record->keys();
						$item = [];
						/** @var $value MapAccessor */
						foreach ($record->values() as $index => $value) {
							if ($value instanceof MapAccessor) {
								foreach ($value->asArray() as $k => $v) {
									$item[$keys[$index] . '.' . $k] = $v;
								}
								continue;
							}
							yield [$keys[$index] => $value];
						}
						yield $item;
					}
				})($this->session->run($query, $params));
			} catch (Throwable $throwable) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($throwable);
			}
		}

		/** @inheritdoc */
		public function exec($query, array $params = []) {
			return $this->fetch($query, $params);
		}

		/** @inheritdoc */
		public function query(IQuery $query, array $binds = []): Generator {
			$schemas = $this->getSchemas($query);
			$selects = $query->getSelects();
			$params = [];
			foreach ($query->params($binds) as $name => $param) {
				$hash = $param->getHash();
				$schema = $schemas[$selects[$param->getAlias()]];
				$attribute = $schema->getAttribute($param->getProperty());
				if (is_iterable($value = $param->getValue()) === false) {
					$params[$hash] = $this->filterValue($attribute, $param->getValue());
					continue;
				}
				foreach ($value as $v) {
					$params[$hash][] = $this->filterValue($attribute, $v);
				}
			}
			foreach ($this->fetch($this->compiler()->compile($query), $params) as $row) {
				yield $this->row($row, $schemas, $selects);
			}
		}

		/** @inheritdoc */
		public function compiler(): ICompiler {
			return $this->compiler ?: $this->compiler = $this->container->create(Neo4jCompiler::class, [], __METHOD__);
		}

		/** @inheritdoc */
		public function create(string $name): IStorage {
			try {
				$schema = $this->schemaManager->getSchema($name);
				$node = $this->delimit($schema->getRealName());
				foreach ($schema->getAttributes() as $name => $property) {
					$fragment = 'n.' . $this->delimit($property->getName());
					if ($property->isPrimary()) {
						$this->fetch('CREATE CONSTRAINT ON (n:' . $node . ') ASSERT (' . $fragment . ') IS NODE KEY');
					} else if ($property->isUnique()) {
						$this->fetch('CREATE CONSTRAINT ON (n:' . $node . ') ASSERT ' . $fragment . ' IS UNIQUE');
					}
					if ($property->isRequired()) {
						$this->fetch('CREATE CONSTRAINT ON (n:' . $node . ') ASSERT exists(' . $fragment . ')');
					}
				}
				return $this;
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function insert(IEntity $entity): IStorage {
			$schema = $entity->getSchema();
			if ($schema->isRelation()) {
				return $this->relation($entity);
			}
			$source = $this->prepareInsert($entity);
			$this->fetch(
				vsprintf('CREATE (a: %s {%s: $primary}) SET a = $set', [
					$this->delimit($schema->getRealName()),
					$this->delimit($primary = $schema->getPrimary()->getName()),
				]),
				[
					'primary' => $source->{$primary},
					'set'     => (array)$source,
				]
			);
			$entity->put($this->prepareOutput($schema, $source));
			$entity->commit();
			return $this;
		}

		/** @inheritdoc */
		public function update(IEntity $entity): IStorage {
			/**
			 * despite duplicate piece of code it's better to keep two almost-same pieces than one with strange behavior related to one
			 * storage system
			 */
			$schema = $entity->getSchema();
			if ($schema->isRelation()) {
				return $this->relation($entity);
			}
			$source = $this->prepareUpdate($entity);
			$this->fetch(
				vsprintf('MERGE (a: %s {%s: $primary}) SET a = $set', [
					$this->delimit($schema->getRealName()),
					$this->delimit($primary = $schema->getPrimary()->getName()),
				]),
				[
					'primary' => $source->{$primary},
					'set'     => (array)$source,
				]
			);
			$entity->put($this->prepareOutput($schema, $source));
			$entity->commit();
			return $this;
		}

		/** @inheritdoc */
		public function save(IEntity $entity): IStorage {
			try {
				$schema = $entity->getSchema();
				if ($schema->isRelation()) {
					return $this->relation($entity);
				}
				$primary = $entity->getPrimary();
				$attribute = $entity->getPrimary()->getAttribute();
				if ($primary->get() === null) {
					return $this->insert($entity);
				}
				$count = ['count' => 0];
				foreach ($this->fetch('MATCH (n:' . $this->delimit($schema->getRealName()) . ' {' . $this->delimit($attribute->getName()) . ': $primary}) RETURN count(n) AS count', ['primary' => $primary->get()]) as $count) {
					break;
				}
				if ($count['count'] === 0) {
					return $this->insert($entity);
				}
				return $this->update($entity);
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function load(string $schema, string $id): IEntity {
			try {
				$schema = $this->schemaManager->getSchema($schema);
				$primary = $schema->getPrimary();
				$query = 'MATCH (n:' . $this->delimit($schema->getRealName()) . ' {' . $this->delimit($primary->getName()) . ': $primary}) RETURN n';
				if ($schema->isRelation()) {
					$query = 'MATCH ()-[n:' . $this->delimit($schema->getRealName()) . ' {' . $this->delimit($primary->getName()) . ': $primary}]-() RETURN n';
				}
				foreach ($this->fetch($query, ['primary' => $id]) as $item) {
					$entity = new Entity($schema);
					$entity->push($this->row($item, ['schema' => $schema], ['n' => 'schema'])->getItem('n'));
					return $entity;
				}
				throw new EntityNotFoundException(sprintf('Cannot load any entity [%s] with id [%s].', $schema->getName(), $id));
			} catch (EntityNotFoundException $exception) {
				throw $exception;
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function unlink(IEntity $entity, IEntity $target, string $relation): IStorage {
			$this->checkRelation(
				$relationSchema = $this->schemaManager->getSchema($relation),
				$entitySchema = $entity->getSchema(),
				$targetSchema = $target->getSchema()
			);
			$this->fetch(
				'MATCH (:' . $this->delimit($entitySchema->getRealName()) . ' {' . $this->delimit($entitySchema->getPrimary()->getName()) . ': $a})-[r:' . $this->delimit($relationSchema->getRealName()) . ']->(:' . $this->delimit($targetSchema->getRealName()) . ' {' . $this->delimit($entitySchema->getPrimary()->getName()) . ': $b}) DETACH DELETE r',
				[
					'a' => $entity->getPrimary()->get(),
					'b' => $target->getPrimary()->get(),
				]
			);
			return $this;
		}

		/** @inheritdoc */
		public function delete(IEntity $entity): IStorage {
			$schema = $entity->getSchema();
			$primary = $entity->getPrimary();
			$this->fetch(
				'MATCH (n:' . $this->delimit($schema->getRealName()) . ' {' . $this->delimit($primary->getAttribute()->getName()) . ': $primary}) DETACH DELETE n',
				[
					'primary' => $primary->get(),
				]
			);
			return $this;
		}

		/** @inheritdoc */
		public function onStart(): void {
			($this->transaction = $this->session->transaction())->begin();
		}

		/** @inheritdoc */
		public function onCommit(): void {
			try {
				$this->transaction->commit();
				$this->transaction = null;
			} catch (Throwable $throwable) {
				$this->exception($throwable);
			}
		}

		/** @inheritdoc */
		public function onRollback(): void {
			try {
				$this->transaction->rollback();
			} catch (MessageFailureException $exception) {
				/**
				 * this is incredibly ugly, but transaction state should be tracked in this driver, so it's
				 * possible to suppress this transaction; related to Neo4j, it's dying on transaction commit, thus
				 * making whole this stuff a bit more complicated
				 */
				if ($exception->getMessage() !== 'No current transaction to rollback.') {
					throw $exception;
				}
			}
			$this->transaction = null;
		}

		/**
		 * @param IEntity $entity
		 *
		 * @return IStorage
		 *
		 * @throws FilterException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 */
		protected function relation(IEntity $entity): IStorage {
			$schema = $entity->getSchema();
			$primary = $schema->getPrimary();
			$sourceAttribute = $schema->getSource();
			$targetAttribute = $schema->getTarget();
			$sourceSchema = $this->schemaManager->getSchema($sourceAttribute->getSchema());
			$targetSchema = $this->schemaManager->getSchema($targetAttribute->getSchema());
			$cypher = null;
			$cypher .= 'MATCH (a:' . $this->delimit($sourceSchema->getRealName()) . ' {' . $this->delimit($sourceSchema->getPrimary()->getName()) . ": \$a})\n";
			$cypher .= 'MATCH (b:' . $this->delimit($targetSchema->getRealName()) . ' {' . $this->delimit($targetSchema->getPrimary()->getName()) . ": \$b})\n";
			$cypher .= 'MERGE (a)-[r:' . $this->delimit($schema->getRealName()) . ' {' . $this->delimit($primary->getName()) . ": \$primary}]->(b)\n";
			$cypher .= 'SET r = $set';
			$source = $this->prepareInsert($entity);
			$this->fetch($cypher, [
				'a'       => $entity->get($sourceAttribute->getName()),
				'b'       => $entity->get($targetAttribute->getName()),
				'primary' => $source->{$primary->getName()},
				'set'     => (array)$source,
			]);
			$entity->put($this->prepareOutput($schema, $source));
			$entity->commit();
			return $this;
		}

		protected function exception(Throwable $throwable): Throwable {
			if (stripos($message = $throwable->getMessage(), 'already exists with label') !== false) {
				return new DuplicateEntryException($message, 0, $throwable);
			} else if (stripos($message, 'must have the property') !== false) {
				return new RequiredValueException($message, 0, $throwable);
			}
			return $throwable;
		}

		/** @inheritdoc */
		public function delimit(string $delimit): string {
			return '`' . str_replace('`', '``', $delimit) . '`';
		}

		/**
		 * @inheritdoc
		 *
		 * @throws ConfigException
		 */
		protected function handleSetup(): void {
			parent::handleSetup();
			$config = null;
			if ($user = $this->section->optional('user')) {
				$config = Configuration::create()->withCredentials($user, $this->section->require('password'));
			}
			$this->session = GraphDatabase::driver($this->section->require('url'), $config)->session();
		}
	}
