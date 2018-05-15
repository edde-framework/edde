<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Edde;
	use Edde\Query\ISelectQuery;
	use Edde\Service\Collection\EntityManager;
	use Edde\Service\Container\Container;
	use Edde\Service\Storage\Storage;
	use Edde\Service\Transaction\Transaction;
	use stdClass;

	class Collection extends Edde implements ICollection {
		use Container;
		use Transaction;
		use Storage;
		use EntityManager;
		/** @var ISelectQuery */
		protected $selectQuery;

		/**
		 * @param ISelectQuery $selectQuery
		 */
		public function __construct(ISelectQuery $selectQuery) {
			$this->selectQuery = $selectQuery;
		}

		/** @inheritdoc */
		public function getSelectQuery(): ISelectQuery {
			return $this->selectQuery;
		}

		/** @inheritdoc */
		public function create(): ICollection {
			$this->transaction->transaction(function () {
				foreach ($this->selectQuery->getSchemas() as $schema) {
					$this->storage->create($schema);
				}
			});
			return $this;
		}

		/** @inheritdoc */
		public function insert(string $alias, stdClass $source): IEntity {
			return $this->entityManager->entity(
				$schema = $this->selectQuery->getSchema($alias),
				$this->storage->insert($schema, $source)
			);
		}

		/** @inheritdoc */
		public function getIterator() {
			$uses = $this->selectQuery->getSchemas();
			foreach ($this->storage->execute($this->selectQuery) as $row) {
				yield $this->container->create(Record::class, [$row, $uses], __METHOD__);
			}
		}
	}
