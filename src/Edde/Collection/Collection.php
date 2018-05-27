<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Edde;
	use Edde\Query\IQuery;
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
		public function use(string $schema, string $alias = null): ICollection {
			$this->query->use($schema, $alias);
			return $this;
		}

		/** @inheritdoc */
		public function create(): ICollection {
			$this->transaction->transaction(function () {
				foreach ($this->query->getSchemas() as $schema) {
					$this->storage->create($schema);
				}
			});
			return $this;
		}

		/** @inheritdoc */
		public function insert(string $alias, stdClass $source): IEntity {
			return $this->entityManager->entity(
				$schema = $this->query->getSchema($alias),
				$this->storage->insert($schema, $source)
			);
		}

		/** @inheritdoc */
		public function getIterator() {
			$uses = $this->query->getSchemas();
			foreach ($this->storage->query($this->query) as $row) {
				$entities = [];
				foreach ($row->getItems() as $alias => $item) {
					$entities[$alias] = $this->entityManager->entity($uses[$alias], $item);
				}
				yield new Record($row, $entities);
			}
		}
	}
