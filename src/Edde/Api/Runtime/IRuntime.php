<?php
	declare(strict_types=1);
	namespace Edde\Api\Runtime;

	use Edde\Exception\Runtime\MissingArgvException;

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
		 * @throws \Edde\Exception\Runtime\MissingArgvException
		 */
		public function getArguments(): array;
	}
