<?php
	declare(strict_types=1);
	namespace Edde\Api\Upgrade;

	use Edde\Config\IConfigurable;
	use Throwable;

	interface IUpgrade extends IConfigurable {
		/**
		 * return a version of this upgrade; it could be arbitrary string as it
		 * it's not used for ordering (should not be used)
		 *
		 * @return string
		 */
		public function getVersion(): string;

		/**
		 * do some stuff before actual upgrade is executed
		 */
		public function onStart(): void;

		/**
		 * execute an upgrade procedure (there could be anything, file moves, cache
		 * invalidation, database structure update, ...)
		 */
		public function upgrade(): void;

		/**
		 * do stuff when an upgrade is successful
		 */
		public function onSuccess(): void;

		/**
		 * what to do, when shit happens
		 *
		 * @param Throwable $throwable
		 *
		 * @throws Throwable
		 */
		public function onFail(Throwable $throwable): void;
	}
