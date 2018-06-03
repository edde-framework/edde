<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\Entity;
	use Edde\Collection\IEntity;
	use Edde\Edde;
	use Edde\Query\IQuery;
	use Edde\Service\Storage\StorageFilterService;
	use stdClass;
	use function explode;
	use function strpos;

	class Record extends Edde implements IRecord {
		use StorageFilterService;
		/** @var IQuery */
		protected $query;
		/** @var array */
		protected $items;
		/** @var IEntity[] */
		protected $entities;

		/**
		 * @param IQuery     $query
		 * @param stdClass[] $items
		 */
		public function __construct(IQuery $query, array $items) {
			$this->query = $query;
			$this->items = $items;
		}

		/** @inheritdoc */
		public function getQuery(): IQuery {
			return $this->query;
		}

		/** @inheritdoc */
		public function getItem(string $alias) {
			if (isset($this->items[$alias])) {
				return $this->items[$alias];
			}
			foreach ($this->items as $k => $v) {
				if (strpos($k, '.') === false) {
					continue;
				}
				unset($this->items[$k]);
				[$name, $property] = explode('.', $k, 2);
				$this->items[$name] = $this->items[$name] ?? new stdClass();
				$this->items[$name]->$property = $v;
			}
			foreach ($this->items as $name => $item) {
				$this->items[$name] = $this->storageFilterService->output($this->query->getSchema($name), $item);
			}
			if (isset($this->items[$alias]) === false) {
				throw new StorageException(sprintf('Requested unknown item alias from a row [%s]; available aliases are [%s].', $alias, implode(', ', array_keys($this->items))));
			}
			return $this->items[$alias];
		}

		/** @inheritdoc */
		public function getEntity(string $alias): IEntity {
			if (isset($this->entities[$alias])) {
				return $this->entities[$alias];
			}
			$entity = new Entity($this->query->getSchema($alias));
			$entity->push($this->getItem($alias));
			return $this->entities[$alias] = $entity;
		}

		/** @inheritdoc */
		public function getItems(): array {
			return $this->items;
		}
	}
