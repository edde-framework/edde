<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\SimpleObject;
	use stdClass;
	use function array_keys;
	use function implode;

	class Row extends SimpleObject implements IRow {
		/** @var stdClass[] */
		protected $items;

		/**
		 * @param stdClass[] $items
		 */
		public function __construct(array $items) {
			$this->items = $items;
		}

		/** @inheritdoc */
		public function getItem(string $alias): stdClass {
			if (isset($this->items[$alias]) === false) {
				throw new StorageException(sprintf('Requested unknown item alias from a row [%s]; available aliases are [%s].', $alias, implode(', ', array_keys($this->items))));
			}
			return $this->items[$alias];
		}

		/** @inheritdoc */
		public function getItems(): array {
			return $this->items;
		}
	}
