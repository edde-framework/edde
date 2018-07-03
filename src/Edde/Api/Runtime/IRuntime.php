<?php
	declare(strict_types=1);

	namespace Edde\Api\Runtime;

	interface IRuntime {
		/***
		 * @return bool
		 */
		public function isConsoleMode(): bool;
	}
