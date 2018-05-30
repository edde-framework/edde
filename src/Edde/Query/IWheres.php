<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface IWheres {
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

		/**
		 * create a new named chain
		 *
		 * @param string|nuLL $name
		 *
		 * @return IChain
		 *
		 * @throws QueryException
		 */
		public function group(string $name = null): IChain;

		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasGroup(?string $name): bool;
	}
