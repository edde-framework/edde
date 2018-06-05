<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Edde;
	use Edde\Query\IQuery;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\Storage\IRecord;
	use Generator;
	use function array_keys;
	use function implode;
	use function is_int;

	class Collection extends Edde implements ICollection {
		use Storage;
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
		public function attach(string $attach, string $to, string $relation): ICollection {
			$this->query->attach($attach, $to, $relation);
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
			return (int)$this->storage->count($this->query)->getItem($alias);
		}

		/** @inheritdoc */
		public function execute(array $binds = []): Generator {
			yield from $this->storage->query($this->query, $binds);
		}

		/** @inheritdoc */
		public function getEntity(string $alias, array $binds = []): IEntity {
			/** @var $record IRecord */
			foreach ($this->execute($binds) as $record) {
				return $record->getEntity($alias);
			}
			throw new EntityNotFoundException(sprintf('Cannot get any entity [%s] from requested sources [%s].', $alias, implode(', ', array_keys($this->query->getSelects()))));
		}
	}
