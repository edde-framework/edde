<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\IEntity;
	use Edde\Config\ConfigException;
	use Edde\Filter\FilterException;
	use Edde\Query\IQuery;
	use Edde\Query\QueryException;
	use Edde\Schema\SchemaException;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use Generator;
	use PDO;
	use PDOException;
	use Throwable;
	use function implode;
	use function is_iterable;
	use function sha1;
	use function sprintf;
	use function vsprintf;

	abstract class AbstractPdoStorage extends AbstractStorage {
		use SchemaManager;
		use Container;
		/** @var PDO */
		protected $pdo;

		/** @inheritdoc */
		public function fetch($query, array $params = []) {
			try {
				$statement = $this->pdo->prepare($query);
				$statement->setFetchMode(PDO::FETCH_ASSOC);
				$statement->execute($params);
				return $statement;
			} catch (PDOException $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function exec($query, array $params = []) {
			try {
				if (empty($params) === false) {
					throw new StorageException(sprintf('%s does not support params.', __METHOD__));
				}
				return $this->pdo->exec($query);
			} catch (PDOException $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function query(IQuery $query, array $binds = []): Generator {
			foreach ($this->fetch($this->compiler->compile($query), $this->params($query, $binds)) as $items) {
				yield $this->container->inject(new Record($query, $items));
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
						$fragment = $this->compiler->delimit($attribute->getName()),
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
						$this->compiler->delimit(sha1($table . '.primary.' . $primary)),
						$primary,
					]);
				}
				$this->exec(vsprintf("CREATE TABLE %s (\n\t%s\n)", [
					$this->compiler->delimit($table),
					implode(",\n\t", $columns),
				]));
				return $this;
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function insert(IEntity $entity): IStorage {
			try {
				$columns = [];
				$params = [];
				foreach ($source = $this->storageFilterService->input($entity->getSchema(), (array)$entity->toObject()) as $k => $v) {
					$columns[sha1($k)] = $this->compiler->delimit($k);
					$params[sha1($k)] = $v;
				}
				$this->fetch(
					vsprintf('INSERT INTO %s (%s) VALUES (:%s)', [
						$this->compiler->delimit(($schema = $entity->getSchema())->getRealName()),
						implode(',', $columns),
						implode(',:', array_keys($params)),
					]),
					$params
				);
				$entity->put($this->storageFilterService->output($schema, $source));
				$entity->commit();
				return $this;
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function update(IEntity $entity): IStorage {
			try {
				$schema = $entity->getSchema();
				$primary = $schema->getPrimary();
				$table = $this->compiler->delimit($schema->getRealName());
				$params = ['primary' => $entity->getPrimary()->get()];
				$columns = [];
				foreach ($source = $this->storageFilterService->update($entity->getSchema(), (array)$entity->toObject()) as $k => $v) {
					$columns[] = $this->compiler->delimit($k) . ' = :' . ($paramId = sha1($k));
					$params[$paramId] = $v;
				}
				$this->fetch(
					vsprintf('UPDATE %s SET %s WHERE %s = :primary', [
						$table,
						implode(', ', $columns),
						$this->compiler->delimit($primary->getName()),
					]),
					$params
				);
				$entity->put($this->storageFilterService->output($schema, $source));
				$entity->commit();
				return $this;
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
		}

		/** @inheritdoc */
		public function save(IEntity $entity): IStorage {
			try {
				$schema = $entity->getSchema();
				$primary = $entity->getPrimary();
				$attribute = $entity->getPrimary()->getAttribute();
				if ($primary->get() === null) {
					return $this->insert($entity);
				}
				$params = [
					$this->compiler->delimit($attribute->getName()),
					$this->compiler->delimit($schema->getRealName()),
					$this->compiler->delimit($attribute->getName()),
				];
				$count = ['count' => 0];
				foreach ($this->fetch(vsprintf('SELECT COUNT(%s) AS count FROM %s WHERE %s = :primary', $params), ['primary' => $primary->get()]) as $count) {
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
		public function delete(IEntity $entity): IStorage {
			$schema = $entity->getSchema();
			$primary = $entity->getPrimary();
			$this->fetch(
				vsprintf('DELETE FROM %s WHERE %s = :primary', [
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
		public function unlink(IEntity $entity, IEntity $target, string $relation): IStorage {
			($relationSchema = $this->schemaManager->getSchema($relation))->checkRelation(
				$entity->getSchema(),
				$target->getSchema()
			);
			$this->fetch(
				vsprintf('DELETE FROM %s WHERE %s = :a AND %s = :b', [
					$this->compiler->delimit($relationSchema->getRealName()),
					$this->compiler->delimit($relationSchema->getSource()->getName()),
					$this->compiler->delimit($relationSchema->getTarget()->getName()),
				]),
				[
					'a' => $entity->getPrimary()->get(),
					'b' => $target->getPrimary()->get(),
				]
			);
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

		/**
		 * @param IQuery $query
		 * @param array  $binds
		 *
		 * @return array
		 *
		 * @throws FilterException
		 * @throws QueryException
		 * @throws SchemaException
		 * @throws StorageException
		 */
		protected function params(IQuery $query, array $binds): array {
			$params = [];
			foreach ($this->storageFilterService->params($query, $binds) as $param) {
				$hash = $param->getHash();
				if (is_iterable($value = $param->getValue()) === false) {
					$params[$hash] = $value;
					continue;
				}
				/**
				 * because we have temp. table and this parameter is not going to be sent to PDO, it's
				 * necessary to kill it
				 */
				unset($params[$hash]);
				/**
				 * this is ugly hack, because of some motherfucker who not implement array support for
				 * WHERE IN clause; so in general it was much more easier to use param name as a temporary
				 * table name from which WHERE IN makes sub-query
				 *
				 * it's not necessary to thanks me
				 */
				$this->exec(vsprintf('CREATE TEMPORARY TABLE %s ( item %s )', [
					$temporary = $this->compiler->delimit($hash),
					$this->type($query->getSchema($param->getAlias())->getAttribute($param->getProperty())->getType()),
				]));
				$statement = $this->pdo->prepare(vsprintf('INSERT INTO %s (item) VALUES (:item)', [
					$temporary,
				]));
				foreach ($value as $v) {
					$statement->execute([
						'item' => $v,
					]);
				}
			}
			return $params;
		}

		/**
		 * @param string $type
		 *
		 * @return string
		 *
		 * @throws StorageException
		 */
		abstract public function type(string $type): string;
	}
