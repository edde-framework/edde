<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Edde;
	use Edde\Query\IQuery;
	use Edde\Service\Collection\EntityManager;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\Service\Transaction\Transaction;
	use Generator;
	use function is_int;

	class Collection extends Edde implements ICollection {
		use Container;
		use Transaction;
		use Storage;
		use EntityManager;
		use SchemaManager;
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
			$this->query->select($this->schemaManager->getSchema($schema), $alias);
			return $this;
		}

		/** @inheritdoc */
		public function selects(array $schemas): ICollection {
			foreach ($schemas as $alias => $schema) {
				$this->select($schema, is_int($alias) ? null : $alias);
			}
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
			$this->query->getSchema($alias);
			return (int)$this->storage->count($this->query)[$alias];
		}

		/** @inheritdoc */
		public function execute(array $binds = []): Generator {
			$selects = $this->query->getSelects();
			foreach ($this->storage->query($this->query, $binds) as $row) {
				$entities = [];
				foreach ($row->getItems() as $alias => $item) {
					$entities[$alias] = $this->entityManager->entity($selects[$alias]->getName(), $item);
				}
				yield new Record($row, $entities);
			}
		}
	}
