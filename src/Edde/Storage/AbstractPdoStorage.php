<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Hydrator\IHydrator;
	use Edde\Service\Security\RandomService;
	use PDO;
	use PDOException;
	use PDOStatement;
	use Throwable;
	use function array_shift;
	use function count;
	use function sha1;
	use function vsprintf;

	abstract class AbstractPdoStorage extends AbstractStorage {
		use RandomService;
		const TYPES = [];
		/** @var PDO */
		protected $pdo;
		/** @var PDOStatement[] */
		protected $statements = [];

		/** @inheritdoc */
		public function fetch(string $query, array $params = []) {
			try {
				if (isset($params['$query'])) {
					$query = $this->query($query, $params['$query']);
					unset($params['$query']);
				}
				if (isset($this->statements[$cacheId = sha1($query)]) === false) {
					$statement = $this->pdo->prepare($query);
					$statement->setFetchMode(PDO::FETCH_ASSOC);
					$this->statements[$cacheId] = $statement;
				}
				if (count($this->statements) >= 64) {
					array_shift($this->statements);
				}
				$this->statements[$cacheId]->execute($params);
				return $this->statements[$cacheId];
			} catch (PDOException $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function exec(string $query, array $params = []) {
			try {
				if (isset($params['$query'])) {
					$query = $this->query($query, $params['$query']);
					unset($params['$query']);
				}
				return $this->pdo->exec($query);
			} catch (PDOException $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function create(string $name): IStorage {
			try {
				$schema = $this->schemaManager->getSchema($name);
				$table = $schema->getRealName();
				$columns = [];
				$primary = null;
				foreach ($schema->getAttributes() as $attribute) {
					$column = vsprintf('%s %s', [
						$fragment = $this->delimit($attribute->getName()),
						$this->type(
							$attribute->hasSchema() ?
								$this->schemaManager->getSchema($attribute->getSchema())->getPrimary()->getType() :
								$attribute->getType()
						),
					]);
					if ($attribute->isPrimary()) {
						$primary = $fragment;
					} else if ($attribute->isUnique()) {
						$column .= ' UNIQUE';
					}
					if ($attribute->isRequired()) {
						$column .= ' NOT NULL';
					}
					$columns[] = $column;
				}
				if ($primary) {
					$columns[] = vsprintf('CONSTRAINT %s PRIMARY KEY (%s)', [
						$this->delimit(sha1($table . '.primary.' . $primary)),
						$primary,
					]);
				}
				$this->exec(vsprintf("CREATE TABLE %s (\n\t%s\n)", [
					$this->delimit($table),
					implode(",\n\t", $columns),
				]));
			} catch (Throwable $exception) {
				throw $this->exception($exception);
			}
			return $this;
		}

		/** @inheritdoc */
		public function creates(array $names): IStorage {
			foreach ($names as $name) {
				$this->create($name);
			}
			return $this;
		}

		/** @inheritdoc */
		public function insert(IEntity $entity, IHydrator $hydrator = null): IEntity {
			try {
				$name = $entity->getSchema();
				$insert = $entity->toArray();
				$hydrator = $hydrator ?: $this->hydratorManager->schema($name);
				$schema = $this->schemaManager->getSchema($name);
				$columns = [];
				$params = [];
				foreach ($source = $hydrator->output($name, $insert) as $k => $v) {
					$columns[sha1($k)] = $this->delimit($k);
					$params[sha1($k)] = $v;
				}
				$this->fetch(
					vsprintf('INSERT INTO %s (%s) VALUES (:%s)', [
						$this->delimit($schema->getRealName()),
						implode(',', $columns),
						implode(',:', array_keys($params)),
					]),
					$params
				);
				return new Entity($name, $hydrator->input($name, $source));
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function inserts(iterable $inserts, IHydrator $hydrator = null): IStorage {
			foreach ($inserts as $insert) {
				$this->insert($insert, $hydrator);
			}
			return $this;
		}

		/** @inheritdoc */
		public function update(IEntity $entity, IHydrator $hydrator = null): IEntity {
			try {
				$name = $entity->getSchema();
				$update = $entity->toArray();
				$hydrator = $hydrator ?: $this->hydratorManager->schema($name);
				$schema = $this->schemaManager->getSchema($name);
				$primary = $schema->getPrimary();
				$table = $this->delimit($schema->getRealName());
				if (isset($update[$primary->getName()]) === false) {
					throw new StorageException(sprintf('Missing primary key [%s] for update!', $primary->getName()));
				}
				$params = ['primary' => $update[$primary->getName()]];
				$columns = [];
				foreach ($source = $hydrator->update($name, $update) as $k => $v) {
					$columns[] = $this->delimit($k) . ' = :' . ($paramId = sha1($k));
					$params[$paramId] = $v;
				}
				$this->fetch(
					vsprintf('UPDATE %s SET %s WHERE %s = :primary', [
						$table,
						implode(', ', $columns),
						$this->delimit($primary->getName()),
					]),
					$params
				);
				return $this->load($name, $params['primary']);
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function save(IEntity $entity, IHydrator $hydrator = null): IEntity {
			try {
				$name = $entity->getSchema();
				$save = $entity->toArray();
				$schema = $this->schemaManager->getSchema($name);
				$primary = $schema->getPrimary();
				if (isset($save[$primaryName = $primary->getName()]) === false) {
					return $this->insert($entity, $hydrator);
				}
				$params = [
					$this->delimit($primaryName),
					$this->delimit($schema->getRealName()),
					$this->delimit($primaryName),
				];
				$count = 0;
				foreach ($this->value(vsprintf('SELECT COUNT(%s) AS count FROM %s WHERE %s = :primary', $params), ['primary' => $save[$primaryName]]) as $count) {
					break;
				}
				if ($count === 0) {
					return $this->insert($entity, $hydrator);
				}
				return $this->update($entity, $hydrator);
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function attach(IEntity $source, IEntity $target, string $relation): IEntity {
			($relationSchema = $this->schemaManager->getSchema($relation))->checkRelation(
				$sourceSchema = $this->schemaManager->getSchema($source->getSchema()),
				$targetSchema = $this->schemaManager->getSchema($target->getSchema())
			);
			$query = vsprintf('SELECT (SELECT COUNT(*) FROM %s WHERE %s = :source LIMIT 1)+(SELECT COUNT(*) FROM %s WHERE %s = :target LIMIT 1)', [
				$this->delimit($sourceSchema->getRealName()),
				$this->delimit($sourceSchema->getPrimary()->getName()),
				$this->delimit($targetSchema->getRealName()),
				$this->delimit($targetSchema->getPrimary()->getName()),
			]);
			$sourceUuid = $source[$sourceSchema->getPrimary()->getName()];
			$targetUuid = $target[$targetSchema->getPrimary()->getName()];
			$item = 0;
			foreach ($this->value($query, ['source' => $sourceUuid, 'target' => $targetUuid]) as $item) {
				break;
			}
			if ($item !== 2) {
				throw new StorageException(sprintf('Source [%s] uuid [%s], target [%s] uuid [%s] or both are not saved.', $sourceSchema->getName(), $sourceUuid, $targetSchema->getName(), $targetUuid));
			}
			return new Entity($relation, [
				$relationSchema->getSource()->getName() => $sourceUuid,
				$relationSchema->getTarget()->getName() => $targetUuid,
			]);
		}

		/** @inheritdoc */
		public function link(IEntity $source, IEntity $target, string $relation): IEntity {
			$this->unlink($source, $target, $relation);
			return $this->attach($source, $target, $relation);
		}

		/** @inheritdoc */
		public function unlink(IEntity $source, IEntity $target, string $relation): IStorage {
			($relationSchema = $this->schemaManager->getSchema($relation))->checkRelation(
				$sourceSchema = $this->schemaManager->getSchema($source->getSchema()),
				$targetSchema = $this->schemaManager->getSchema($target->getSchema())
			);
			$sourceUuid = $source[$sourceSchema->getPrimary()->getName()];
			$targetUuid = $target[$targetSchema->getPrimary()->getName()];
			$this->fetch(
				vsprintf('DELETE FROM %s WHERE %s = :a AND %s = :b', [
					$this->delimit($relationSchema->getRealName()),
					$this->delimit($relationSchema->getSource()->getName()),
					$this->delimit($relationSchema->getTarget()->getName()),
				]),
				[
					'a' => $sourceUuid,
					'b' => $targetUuid,
				]
			);
			return $this;
		}

		/** @inheritdoc */
		public function load(string $name, string $uuid): IEntity {
			$schema = $this->schemaManager->getSchema($name);
			foreach ($this->schema($name, sprintf('SELECT * FROM %s WHERE %s = :uuid', $this->delimit($schema->getRealName()), $schema->getPrimary()->getName()), ['uuid' => $uuid]) as $entity) {
				return $entity;
			}
			throw new EmptyEntityException(sprintf('Requested unknown uuid [%s] of [%s].', $uuid, $name));
		}

		/** @inheritdoc */
		public function temporal(string $type, iterable $items, callable $callback): iterable {
			$table = $this->randomService->generate(64);
			$this->start();
			$this->exec($this->query(sprintf('CREATE TABLE %s:delimit ( item %s )', $table, $this->type($type))));
			foreach ($items as $uuid) {
				$this->fetch($this->query(sprintf('INSERT INTO %s:delimit (item) VALUES (:item)', $table)), [
					'item' => $uuid,
				]);
			}
			yield from $callback($table);
			$this->rollback();
		}

		/** @inheritdoc */
		public function delete(IEntity $entity): IStorage {
			$schema = $this->schemaManager->getSchema($entity->getSchema());
			$primary = $schema->getPrimary();
			$this->fetch(
				vsprintf('DELETE FROM %s WHERE %s = :primary', [
					$this->delimit($schema->getRealName()),
					$this->delimit($primary->getName()),
				]),
				[
					'primary' => $entity->toArray()[$primary->getName()],
				]
			);
			return $this;
		}

		/** @inheritdoc */
		public function reconnect(): IStorage {
			$this->pdo = null;
			$this->pdo = new PDO(
				$this->section->require('dsn'),
				$this->section->require('user'),
				$this->section->optional('password')
			);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
			$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
			$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
			$this->pdo->setAttribute(PDO::ATTR_TIMEOUT, 120);
			return $this;
		}

		/** @inheritdoc */
		public function onStart(): void {
			$this->pdo->beginTransaction();
		}

		/** @inheritdoc */
		public function onCommit(): void {
			$this->pdo->commit();
		}

		/** @inheritdoc */
		public function onRollback(): void {
			$this->pdo->rollBack();
		}

		/** @inheritdoc */
		protected function handleSetup(): void {
			parent::handleSetup();
			$this->reconnect();
		}

		/**
		 * @param string $type
		 *
		 * @return string
		 *
		 * @throws StorageException
		 */
		protected function type(string $type): string {
			if (isset(static::TYPES[$type = strtolower($type)])) {
				return static::TYPES[$type];
			}
			throw new StorageException(sprintf('Unknown type [%s] ', $type, static::class));
		}
	}
