<?php
	declare(strict_types=1);
	namespace Edde\Log;

	use Edde\Configurable\IConfigurable;
	use Throwable;

	/**
	 * Target of log item
	 */
	interface ILogger extends IConfigurable {
		/**
		 * shortcut for record();
		 *
		 * @param mixed $log
		 * @param array $tags
		 */
		public function log($log, array $tags = []): void;

		/**
		 * @param ILog       $log
		 * @param array|null $tags
		 */
		public function record(ILog $log, array $tags = []): void;

		/**
		 * adds informative tag
		 *
		 * @param string $log
		 * @param array  $tags
		 */
		public function info(string $log, array $tags = []): void;

		/**
		 * adds warning tag
		 *
		 * @param string $log
		 * @param array  $tags
		 */
		public function warning(string $log, array $tags = []): void;

		/**
		 * adds error tag
		 *
		 * @param string $log
		 * @param array  $tags
		 */
		public function error(string $log, array $tags = []): void;

		/**
		 * adds critical tag
		 *
		 * @param string $log
		 * @param array  $tags
		 */
		public function critical(string $log, array $tags = []): void;

		/**
		 * log an exception
		 *
		 * @param Throwable  $exception
		 * @param array|null $tags
		 */
		public function exception(Throwable $exception, array $tags = []): void;
	}
