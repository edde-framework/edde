<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface IGroup {
		/**
		 * @param string $name
		 *
		 * @return IChain
		 */
		public function chain(string $name): IChain;
	}
