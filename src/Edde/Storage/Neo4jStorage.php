<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\Entity;
	use Edde\Collection\EntityNotFoundException;
	use Edde\Collection\IEntity;
	use Edde\Config\ConfigException;
	use Edde\Filter\FilterException;
	use Edde\Query\ISelectQuery;
	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Security\RandomService;
	use Generator;
	use GraphAware\Bolt\Configuration;
	use GraphAware\Bolt\Exception\MessageFailureException;
	use GraphAware\Bolt\GraphDatabase;
	use GraphAware\Bolt\Protocol\SessionInterface;
	use GraphAware\Bolt\Protocol\V1\Transaction;
	use GraphAware\Bolt\Result\Result;
	use GraphAware\Common\Type\MapAccessor;
	use Throwable;
	use function implode;

	class Neo4jStorage extends AbstractStorage {
		use SchemaManager;
		use RandomService;
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
			$source = $this->prepareInsert($entity);
			$schema = $entity->getSchema();
			$this->fetch(
				'MERGE (a:' . $this->delimit($schema->getRealName()) . ' {' . $this->delimit($primary = $schema->getPrimary()->getName()) . ': $primary}) SET a = $set',
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
			$source = $this->prepareUpdate($entity);
			$schema = $entity->getSchema();
			$this->fetch(
				'MERGE (a:' . $this->delimit($schema->getRealName()) . ' {' . $this->delimit($primary = $schema->getPrimary()->getName()) . ': $primary}) SET a = $set',
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
				$primary = $schema->getPrimary()->getName();
				if (property_exists($source, $primary) === false || $source->$primary === null) {
					return $this->insert($schema->getName(), $source);
				}
				$count = ['count' => 0];
				foreach ($this->fetch('MATCH (n:' . $this->delimit($schema->getRealName()) . ' {' . $this->delimit($primary) . ': $primary}) RETURN count(n) AS count', ['primary' => $source->$primary]) as $count) {
					break;
				}
				if ($count['count'] === 0) {
					return $this->insert($schema->getName(), $source);
				}
				return $this->update($schema->getName(), $source);
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
				foreach ($this->fetch($query, ['primary' => $id]) as $item) {
					$entity = new Entity($schema);
					$entity->push($this->row($item, ['schema' => $schema], ['n' => 'schema'])->getItem('n'));
					return $entity;
				}
				throw new EntityNotFoundException(sprintf('Cannot load any entity [%s] with id [%s].', $schema, $id));
			} catch (EntityNotFoundException $exception) {
				throw $exception;
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
		}

		/**
		 * @param ISelectQuery $selectQuery
		 *
		 * @return Generator
		 * @throws StorageException
		 * @throws FilterException
		 * @throws SchemaException
		 */
		protected function executeSelect(ISelectQuery $selectQuery): Generator {
			$cypher = "MATCH\n";
			$returns = [];
			$params = [];
			$uses = $selectQuery->getSchemas();
			/** @var $schemas ISchema[] */
			$schemas = [];
			foreach (array_unique(array_values($uses)) as $schema) {
				$schemas[$schema] = $this->schemaManager->getSchema($schema);
			}
			$from = [];
			foreach ($uses as $alias => $schema) {
				$from[] = '(' . ($returns[] = $this->delimit($alias)) . ':' . $this->delimit($schemas[$schema]->getRealName()) . ')';
			}
			$cypher .= "\t" . implode(",\n", $from) . "\nRETURN\n\t" . implode(',', $returns);
			foreach ($this->fetch($cypher, $params) as $row) {
				yield $this->row($row, $schemas, $uses);
			}
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
