<?php
	declare(strict_types=1);

	namespace Edde\Api\Upgrade;

	/**
	 * Simple way how to run arbitrary application upgrades; this can be storage modification, filesystem operations, ...
	 */
	interface IUpgrade {
		/**
		 * version can be arbitrary string; for IUpgradeManager is important order of versions (they should NOT be parsed)
		 *
		 * @return string
		 */
		public function getVersion();

		/**
		 * run this particular upgrade
		 *
		 * @return $this
		 *
		 * @throws UpgradeException
		 */
		public function upgrade();
	}
