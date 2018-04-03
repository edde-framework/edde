<?php
	declare(strict_types=1);
	namespace Edde\Service\Upgrade;

	use Edde\Upgrade\IUpgradeManager;

	trait UpgradeManager {
		/** @var IUpgradeManager */
		protected $upgradeManager;

		/**
		 * @param IUpgradeManager $upgradeManager
		 */
		public function injectUpgradeManager(IUpgradeManager $upgradeManager) {
			$this->upgradeManager = $upgradeManager;
		}
	}
