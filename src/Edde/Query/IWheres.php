<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface IWheres {
		/**
		 * add a name where to be later used in query (where itself is not active)
		 *
		 * @param string $name
		 * @param bool   $force if where exists, force replace
		 *
		 * @return IWhere
		 *
		 * @throws QueryException if where is already registered and $force is false
		 */
		public function where(string $name, bool $force = false): IWhere;

		/**
		 * are there some wheres?
		 *
		 * @return bool
		 */
		public function isEmpty(): bool;

		/**
		 * return internal where objects
		 *
		 * @return IWhere[]
		 */
		public function getWheres(): array;
	}
