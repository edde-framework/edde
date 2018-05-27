<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\SimpleObject;
	use Edde\Storage\IRow;
	use stdClass;

	class Record extends SimpleObject implements IRecord {
		/** @var IRow */
		protected $row;
		/** @var IEntity[] */
		protected $entities = [];

		/**
		 * @param IRow      $row
		 * @param IEntity[] $entities
		 */
		public function __construct(IRow $row, array $entities) {
			$this->row = $row;
			$this->entities = $entities;
		}

		/** @inheritdoc */
		public function getRow(): IRow {
			return $this->row;
		}

		/** @inheritdoc */
		public function getItem(string $alias): stdClass {
			return $this->row->getItem($alias);
		}

		/** @inheritdoc */
		public function getEntity(string $alias): IEntity {
			if (isset($this->entities[$alias]) === false) {
				throw new CollectionException(sprintf('Requested unknown entity alias from a record [%s]; available aliases are [%s].', $alias, implode(', ', array_keys($this->entities))));
			}
			return $this->entities[$alias];
		}
	}
