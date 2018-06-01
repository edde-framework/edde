<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface IBinds {
		/**
		 * @return IBind[]
		 */
		public function getBinds(): array;
	}
