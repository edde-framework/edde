<?php
	declare(strict_types = 1);

	namespace Edde\Common\Upgrade\Event;

	use Edde\Api\Upgrade\IUpgrade;
	use Edde\Api\Upgrade\IUpgradeManager;

	class UpgradeFailEvent extends UpgradeEvent {
		/**
		 * @var IUpgrade
		 */
		protected $upgrade;

		/**
		 * @param IUpgradeManager $upgradeManager
		 * @param IUpgrade $upgrade
		 */
		public function __construct(IUpgradeManager $upgradeManager, IUpgrade $upgrade) {
			parent::__construct($upgradeManager);
			$this->upgrade = $upgrade;
		}

		public function getUpgrade(): IUpgrade {
			return $this->upgrade;
		}
	}
