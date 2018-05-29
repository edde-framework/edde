<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface IChain {
		/**
		 * select a where of this chain (to make relations clear)
		 *
		 * @param string $name
		 *
		 * @return IChain
		 *
		 * @throws QueryException if where is not registered
		 */
		public function select(string $name): IChain;

		/**
		 * @param string $name
		 *
		 * @return IChain
		 */
		public function and(string $name): IChain;

		/**
		 * @param string $name
		 *
		 * @return IChain
		 */
		public function or(string $name): IChain;

		/**
		 * convert current (selected) where to a group; for example: "foo AND bar" -> "(foo) AND bar" and when chain is used again, like
		 * select(foo)->and(moo), result will be "(foo and moo) AND bar"
		 *
		 * @param string $name
		 *
		 * @return IChain
		 */
		public function group(string $name): IChain;
	}
