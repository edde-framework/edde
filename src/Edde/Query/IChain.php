<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface IChain {
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
	}
