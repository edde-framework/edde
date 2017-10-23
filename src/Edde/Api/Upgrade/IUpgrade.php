<?php
	namespace Edde\Api\Upgrade;

		use Edde\Api\Config\IConfigurable;

		interface IUpgrade extends IConfigurable {
			/**
			 * return a version of this upgrade; it could be arbitrary string as it
			 * it's not used for ordering (should not be used)
			 *
			 * @return string
			 */
			public function getVersion(): string;

			/**
			 * execute an upgrade procedure (there could be anything, file moves, cache
			 * invalidation, database structure update, ...)
			 */
			public function upgrade(): void;

			/**
			 * try to revert effect of this upgrade; drop newly created columns, rename
			 * tables, delete new files, ...
			 */
			public function rollback(): void;
		}
