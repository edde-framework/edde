<?php
	declare(strict_types=1);
	namespace Edde\Sql;

	use Edde\Hydrator\IHydrator;
	use Edde\Query\AbstractQuery;
	use Edde\Service\Filter\FilterManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\Service\Validator\ValidatorManager;
	use Edde\Transaction\TransactionException;
	use Throwable;

	class InsertQuery extends AbstractQuery {
		use Storage;
		use SchemaManager;
		use FilterManager;
		use ValidatorManager;
		/** @var IHydrator */
		protected $hydrator;
		/** @var string */
		protected $prefix;

		/**
		 * @param IHydrator $hydrator
		 * @param string    $prefix
		 */
		public function __construct(IHydrator $hydrator, string $prefix = 'storage') {
			$this->hydrator = $hydrator;
			$this->prefix = $prefix;
		}

		/**
		 * @param string $name
		 * @param array  $insert
		 *
		 * @return array
		 *
		 * @throws Throwable
		 */
		public function insert(string $name, array $insert): array {
			try {
				$schema = $this->schemaManager->getSchema($name);
				$columns = [];
				$params = [];
				foreach ($source = $this->hydrator->input($name, $insert) as $k => $v) {
					$columns[sha1($k)] = $this->storage->delimit($k);
					$params[sha1($k)] = $v;
				}
				if (empty($params)) {
					return [];
				}
				$this->storage->fetch(
					vsprintf('INSERT INTO %s (%s) VALUES (:%s)', [
						$this->storage->delimit($schema->getRealName()),
						implode(',', $columns),
						implode(',:', array_keys($params)),
					]),
					$params
				);
				return $this->hydrator->output($name, $source);
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->storage->exception($exception);
			}
		}

		/**
		 * @param string $name
		 * @param array  $inserts
		 *
		 * @throws TransactionException
		 */
		public function inserts(string $name, array $inserts): void {
			$this->storage->transaction(function () use ($name, $inserts) {
				foreach ($inserts as $insert) {
					$this->insert($name, $insert);
				}
			});
		}
	}
