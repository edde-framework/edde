<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Config\ConfigException;
	use Edde\Hydrator\IHydrator;
	use Edde\Service\Schema\SchemaManager;
	use PDO;
	use PDOException;
	use Throwable;

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
