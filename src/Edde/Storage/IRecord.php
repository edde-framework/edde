<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\IEntity;
	use Edde\Filter\FilterException;
	use Edde\Query\IQuery;
	use Edde\Query\QueryException;
	use Edde\Schema\SchemaException;
	use Edde\Validator\ValidatorException;

	/**
	 * Just one "wide" row from storage (could be more tables joined together).
	 */
	interface IRecord {
		/**
		 * return source query
		 *
		 * @return IQuery
		 */
		public function getQuery(): IQuery;

		/**
		 * @param string $alias
		 *
		 * @return mixed
		 *
		 * @throws StorageException
		 * @throws QueryException
		 * @throws SchemaException
		 * @throws FilterException
		 * @throws ValidatorException
		 */
		public function getItem(string $alias);

		/**
		 * @param string $alias
		 *
		 * @return IEntity
		 *
		 * @throws StorageException
		 */
		public function getEntity(string $alias): IEntity;

		/**
		 * @return array
		 */
		public function getItems(): array;
	}
