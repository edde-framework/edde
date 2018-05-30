<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface IChains {
		/**
		 * create a new named chain
		 *
		 * @param string|nuLL $name
		 *
		 * @return IChain
		 *
		 * @throws QueryException
		 */
		public function chain(string $name = null): IChain;

		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasChain(?string $name): bool;

		/**
		 * @return IChain[]
		 */
		public function getChains(): array;
	}
