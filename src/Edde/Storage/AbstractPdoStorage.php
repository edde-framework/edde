<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Config\ConfigException;
	use Edde\Hydrator\IHydrator;
	use Edde\Service\Schema\SchemaManager;
	use PDO;
	use PDOException;
	use Throwable;
	use function key;
	use function reset;
	use function vsprintf;

	abstract class AbstractPdoStorage extends AbstractStorage {
		use SchemaManager;
		/** @var PDO */
		protected $pdo;

		/** @inheritdoc */
		public function fetch(string $query, array $params = []) {
			try {
				$statement = $this->pdo->prepare($query);
				$statement->setFetchMode(PDO::FETCH_ASSOC);
				$statement->execute($params);
				return $statement;
			} catch (PDOException $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function exec(string $query, array $params = []) {
			try {
				return $this->pdo->exec($query);
			} catch (PDOException $exception) {
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function insert(string $name, array $insert, IHydrator $hydrator = null): array {
			try {
				$hydrator = $hydrator ?: $this->hydratorManager->schema($name);
				$schema = $this->schemaManager->getSchema($name);
				$columns = [];
				$params = [];
				foreach ($source = $hydrator->input($name, $insert) as $k => $v) {
					$columns[sha1($k)] = $this->delimit($k);
					$params[sha1($k)] = $v;
				}
				if (empty($params)) {
					return [];
				}
				$this->fetch(
					vsprintf('INSERT INTO %s (%s) VALUES (:%s)', [
						$this->delimit($schema->getRealName()),
						implode(',', $columns),
						implode(',:', array_keys($params)),
					]),
					$params
				);
				return $hydrator->output($name, $source);
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function inserts(string $name, iterable $inserts, IHydrator $hydrator = null): IStorage {
			$this->transaction(function () use ($name, $inserts, $hydrator) {
				foreach ($inserts as $insert) {
					$this->insert($name, $insert, $hydrator);
				}
			});
			return $this;
		}

		/** @inheritdoc */
		public function update(string $name, array $update, IHydrator $hydrator = null): array {
			try {
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
				return $hydrator->output($name, $source);
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function save(string $name, array $save, IHydrator $hydrator = null): array {
			try {
				$schema = $this->schemaManager->getSchema($name);
				$primary = $schema->getPrimary();
				if (isset($save[$primaryName = $primary->getName()]) === false) {
					return $this->insert($name, $save, $hydrator);
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
					return $this->insert($name, $save, $hydrator);
				}
				return $this->update($name, $save, $hydrator);
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function attach(array $source, array $target, string $relation): array {
			($relationSchema = $this->schemaManager->getSchema($relation))->checkRelation(
				$sourceSchema = $this->schemaManager->getSchema(key($source)),
				$targetSchema = $this->schemaManager->getSchema(key($target))
			);
			$query = vsprintf('SELECT (SELECT COUNT(*) FROM %s WHERE %s = :source LIMIT 1)+(SELECT COUNT(*) FROM %s WHERE %s = :target LIMIT 1)', [
				$this->delimit($sourceSchema->getRealName()),
				$this->delimit($sourceSchema->getPrimary()->getName()),
				$this->delimit($targetSchema->getRealName()),
				$this->delimit($targetSchema->getPrimary()->getName()),
			]);
			$sourceUuid = reset($source);
			$targetUuid = reset($target);
			$item = 0;
			foreach ($this->value($query, ['source' => $sourceUuid, 'target' => $targetUuid]) as $item) {
				break;
			}
			if ($item !== 2) {
				throw new StorageException(sprintf('Source [%s] uuid [%s], target [%s] uuid [%s] or both are not saved.', $sourceSchema->getName(), $sourceUuid, $targetSchema->getName(), $targetUuid));
			}
			return [
				$relationSchema->getSource()->getName() => $sourceUuid,
				$relationSchema->getTarget()->getName() => $targetUuid,
			];
		}

		/** @inheritdoc */
		public function load(string $name, string $uuid): array {
			$schema = $this->schemaManager->getSchema($name);
			foreach ($this->schema($name, sprintf('SELECT * FROM %s WHERE %s = :uuid', $this->delimit($schema->getRealName()), $schema->getPrimary()->getName()), ['uuid' => $uuid]) as $item) {
				return $item;
			}
			throw new UnknownUuidException(sprintf('Requested unknown UUID [%s] of [%s].', $uuid, $name));
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

		/**
		 * @inheritdoc
		 *
		 * @throws ConfigException
		 */
		public function handleSetup(): void {
			parent::handleSetup();
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
		}
	}
