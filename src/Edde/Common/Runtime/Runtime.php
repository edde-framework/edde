<?php
	declare(strict_types=1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Runtime\IRuntime;
	use Edde\Common\Object;

	class Runtime extends Object implements IRuntime {
		/**
		 * @inheritdoc
		 */
		public function isConsoleMode(): bool {
			return PHP_SAPI === 'cli';
		}
	}
