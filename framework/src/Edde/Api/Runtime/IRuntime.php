<?php
	declare(strict_types=1);
	namespace Edde\Api\Runtime;

	use Edde\Api\Runtime\Exception\MissingArgvException;

	interface IRuntime {
		/***
		 * @return bool
		 */
		public function isConsoleMode(): bool;

		/**
		 * return argument list
		 *
		 * @return array
		 *
		 * @throws MissingArgvException
		 */
		public function getArguments(): array;
	}
