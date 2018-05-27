<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Storage\IRow;
	use Edde\Storage\StorageException;
	use stdClass;

	/**
	 * Record is a row got from storage containing multpile entities
	 * separated by an alias (kind of result set).
	 */
	interface IRecord {
		/**
		 * return source row (with all aliases queried)
		 *
		 * @return IRow
		 */
		public function getRow(): IRow;

		/**
		 * return item from a row
		 *
		 * @param string $alias
		 *
		 * @return stdClass
		 *
		 * @throws StorageException
		 */
		public function getItem(string $alias): stdClass;

		/**
		 * @param string $alias
		 *
		 * @return IEntity
		 *
		 * @throws CollectionException
		 */
		public function getEntity(string $alias): IEntity;
	}
