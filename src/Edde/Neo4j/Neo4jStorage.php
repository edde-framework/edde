<?php
	declare(strict_types=1);
	namespace Edde\Neo4j;

	use Edde\Collection\IEntity;
	use Edde\Config\ConfigException;
	use Edde\Filter\FilterException;
	use Edde\Schema\SchemaException;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Storage\AbstractStorage;
	use Edde\Storage\DuplicateEntryException;
	use Edde\Storage\ICompiler;
	use Edde\Storage\IQuery;
	use Edde\Storage\IStorage;
	use Edde\Storage\Neo4jCompiler;
	use Edde\Storage\Record;
	use Edde\Storage\RequiredValueException;
	use Edde\Storage\StorageException;
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
							continue 2;
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
			$params = [];
			foreach ($this->storageFilterService->params($query, $binds) as $param) {
				$params[$param->getHash()] = $param->getValue();
			}
			foreach ($this->fetch($this->compiler->compile($query), $params) as $items) {
				yield $this->container->inject(new Record($query, $items));
			}
		}

		/** @inheritdoc */
		public function create(string $name): IStorage {
			try {
				$schema = $this->schemaManager->getSchema($name);
				$node = $this->compiler->delimit($schema->getRealName());
				foreach ($schema->getAttributes() as $name => $property) {
					$fragment = 'n.' . $this->compiler->delimit($property->getName());
					if ($property->isPrimary()) {
						$this->fetch(vsprintf('CREATE CONSTRAINT ON (n:%s) ASSERT (%s) IS NODE KEY', [
							$node,
							$fragment,
						]));
					} else if ($property->isUnique()) {
						$this->fetch(vsprintf('CREATE CONSTRAINT ON (n:%s) ASSERT %s IS UNIQUE', [
							$node,
							$fragment,
						]));
					}
					if ($property->isRequired()) {
						$this->fetch(vsprintf('CREATE CONSTRAINT ON (n:%s) ASSERT exists(%s)', [
							$node,
							$fragment,
						]));
					}
				}
				return $this;
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function insert(string $schema, array $insert): array {
			$schema = $entity->getSchema();
			if ($schema->isRelation()) {
				return $this->relation($entity);
			}
			$source = $this->storageFilterService->input($entity->getSchema(), (array)$entity->toObject());
			$this->fetch(
				vsprintf('CREATE (a: %s {%s: $primary}) SET a = $set', [
					$this->compiler->delimit($schema->getRealName()),
					$this->compiler->delimit($primary = $schema->getPrimary()->getName()),
				]),
				[
					'primary' => $source[$primary],
					'set'     => $source,
				]
			);
			$entity->put($this->storageFilterService->output($schema, $source));
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
			$source = $this->storageFilterService->update($entity->getSchema(), (array)$entity->toObject());
			$this->fetch(
				vsprintf('MERGE (a: %s {%s: $primary}) SET a = $set', [
					$this->compiler->delimit($schema->getRealName()),
					$this->compiler->delimit($primary = $schema->getPrimary()->getName()),
				]),
				[
					'primary' => $source[$primary],
					'set'     => $source,
				]
			);
			$entity->put($this->storageFilterService->output($schema, $source));
			$entity->commit();
			return $this;
		}

		/** @inheritdoc */
		public function save(string $schema, array $save): array {
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
				$params = [
					$this->compiler->delimit($schema->getRealName()),
					$this->compiler->delimit($attribute->getName()),
				];
				foreach ($this->fetch(vsprintf('MATCH (n:%s {%s: $primary}) RETURN count(n) AS count', $params), ['primary' => $primary->get()]) as $count) {
					break;
				}
				if ($count['count'] === 0) {
					return $this->insert($entity);
				}
				return $this->update($entity);
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function unlink(IEntity $entity, IEntity $target, string $relation): IStorage {
			($relationSchema = $this->schemaManager->getSchema($relation))->checkRelation(
				$entitySchema = $entity->getSchema(),
				$targetSchema = $target->getSchema()
			);
			$this->fetch(
				vsprintf('MATCH (:%s {%s: $a})-[r:%s]->(:%s {%s: $b}) DETACH DELETE r', [
					$this->compiler->delimit($entitySchema->getRealName()),
					$this->compiler->delimit($entitySchema->getPrimary()->getName()),
					$this->compiler->delimit($relationSchema->getRealName()),
					$this->compiler->delimit($targetSchema->getRealName()),
					$this->compiler->delimit($entitySchema->getPrimary()->getName()),
				]),
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
				vsprintf('MATCH (n:%s {%s: $primary}) DETACH DELETE n', [
					$this->compiler->delimit($schema->getRealName()),
					$this->compiler->delimit($primary->getAttribute()->getName()),
				]),
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
			$schema->checkRelation(
				$sourceSchema = $this->schemaManager->getSchema($sourceAttribute->getSchema()),
				$targetSchema = $this->schemaManager->getSchema($targetAttribute->getSchema())
			);
			$source = $this->storageFilterService->input($entity->getSchema(), (array)$entity->toObject());
			$this->fetch(
				vsprintf('MATCH (a:%s {%s: $a}) MATCH (b:%s {%s: $b}) MERGE (a)-[r:%s {%s: $primary}]->(b) SET r = $set', [
					$this->compiler->delimit($sourceSchema->getRealName()),
					$this->compiler->delimit($sourceSchema->getPrimary()->getName()),
					$this->compiler->delimit($targetSchema->getRealName()),
					$this->compiler->delimit($targetSchema->getPrimary()->getName()),
					$this->compiler->delimit($schema->getRealName()),
					$this->compiler->delimit($primary->getName()),
				]),
				[
					'a'       => $entity->get($sourceAttribute->getName()),
					'b'       => $entity->get($targetAttribute->getName()),
					'primary' => $source[$primary->getName()],
					'set'     => $source,
				]
			);
			$entity->put($this->storageFilterService->output($schema, $source));
			$entity->commit();
			return $this;
		}

		/** @inheritdoc */
		public function createCompiler(): ICompiler {
			return $this->compiler ?: $this->compiler = $this->container->inject(new Neo4jCompiler());
		}

		protected function exception(Throwable $throwable): Throwable {
			if (stripos($message = $throwable->getMessage(), 'already exists with label') !== false) {
				return new DuplicateEntryException($message, 0, $throwable);
			} else if (stripos($message, 'must have the property') !== false) {
				return new RequiredValueException($message, 0, $throwable);
			}
			return $throwable;
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
