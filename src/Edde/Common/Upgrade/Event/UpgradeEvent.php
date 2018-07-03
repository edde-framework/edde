<?php
	declare(strict_types = 1);

	namespace Edde\Common\Upgrade\Event;

	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Event\AbstractEvent;

	/**
	 * Base event for any upgrades; this should not be emitted.
	 */
	class UpgradeEvent extends AbstractEvent {
		/**
		 * @var IUpgradeManager
		 */
		protected $upgradeManager;

		/**
		 * @param IUpgradeManager $upgradeManager
		 */
		public function __construct(IUpgradeManager $upgradeManager) {
			$this->upgradeManager = $upgradeManager;
		}

		public function getUpgradeManager(): IUpgradeManager {
			return $this->upgradeManager;
		}
	}
