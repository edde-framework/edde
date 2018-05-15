<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Edde;
	use Edde\Service\Collection\EntityManager;
	use Edde\Storage\IRow;
	use stdClass;

	class Record extends Edde implements IRecord {
		use EntityManager;
		/** @var IRow */
		protected $row;
		protected $schemas;
		protected $entities = [];

		/**
		 * @param IRow     $row
		 * @param string[] $schemas
		 */
		public function __construct(IRow $row, array $schemas) {
			$this->row = $row;
			$this->schemas = $schemas;
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
			if (isset($this->entities[$alias])) {
				return $this->entities[$alias];
			}
			return $this->entities[$alias] = $this->entityManager->entity($this->schemas[$alias], $this->row->getItem($alias));
		}
	}
