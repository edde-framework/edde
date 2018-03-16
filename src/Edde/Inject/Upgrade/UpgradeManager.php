<?php
	declare(strict_types=1);
	namespace Edde\Inject\Upgrade;

	use Edde\Upgrade\IUpgradeManager;

	trait UpgradeManager {
		/**
		 * @var IUpgradeManager
		 */
		protected $upgradeManager;

		/**
		 * @param IUpgradeManager $upgradeManager
		 */
		public function lazyUpgradeManager(IUpgradeManager $upgradeManager) {
			$this->upgradeManager = $upgradeManager;
		}
	}
