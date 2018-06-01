<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Edde;
	use Edde\Query\IQuery;
	use Edde\Service\Collection\EntityManager;
	use Edde\Service\Container\Container;
	use Edde\Service\Storage\Storage;
	use Edde\Service\Transaction\Transaction;
	use Generator;

	class Collection extends Edde implements ICollection {
		use Container;
		use Transaction;
		use Storage;
		use EntityManager;
		/** @var IQuery */
		protected $query;

		/**
		 * @param IQuery $query
		 */
		public function __construct(IQuery $query) {
			$this->query = $query;
		}

		/** @inheritdoc */
		public function getQuery(): IQuery {
			return $this->query;
		}

		/** @inheritdoc */
		public function select(string $schema, string $alias = null): ICollection {
			$this->query->select($schema, $alias);
			return $this;
		}

		/** @inheritdoc */
		public function selects(array $schemas): ICollection {
			$this->query->selects($schemas);
			return $this;
		}

		/** @inheritdoc */
		public function order(string $alias, string $property, string $order = 'asc'): ICollection {
			$this->query->order($alias, $property, $order);
			return $this;
		}

		/** @inheritdoc */
		public function page(int $page, int $size): ICollection {
			$this->query->page($page, $size);
			return $this;
		}

		/** @inheritdoc */
		public function count(string $alias): int {
			/**
			 * ensure that alias is available in a select
			 */
			$this->query->getSelect($alias);
			return (int)$this->storage->count($this->query)[$alias];
		}

		/** @inheritdoc */
		public function execute(array $binds = []): Generator {
			$uses = $this->query->getSelects();
			foreach ($this->storage->query($this->query, $binds) as $row) {
				$entities = [];
				foreach ($row->getItems() as $alias => $item) {
					$entities[$alias] = $this->entityManager->entity($uses[$alias], $item);
				}
				yield new Record($row, $entities);
			}
		}
	}
