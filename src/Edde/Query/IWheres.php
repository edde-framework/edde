<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use IteratorAggregate;
	use Traversable;

	interface IWheres extends IteratorAggregate {
		/**
		 * add a name where to be later used in query (where itself is not active)
		 *
		 * @param string $name
		 *
		 * @return IWhere
		 *
		 * @throws QueryException if where is already registered
		 */
		public function where(string $name): IWhere;

		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasWhere(string $name): bool;

		/**
		 * @param string $name
		 *
		 * @return IWhere
		 *
		 * @throws QueryException
		 */
		public function getWhere(string $name): IWhere;

		/**
		 * return internal where objects
		 *
		 * @return IWhere[]
		 */
		public function getWheres(): array;

		/**
		 * @return IChains
		 */
		public function chains(): IChains;

		/**
		 * @return Traversable|IWhere[]
		 */
		public function getIterator();
	}
