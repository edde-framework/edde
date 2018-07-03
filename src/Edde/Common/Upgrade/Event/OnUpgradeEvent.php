<?php
	declare(strict_types = 1);

	namespace Edde\Common\Upgrade\Event;

	use Edde\Api\Upgrade\IUpgrade;
	use Edde\Api\Upgrade\IUpgradeManager;

	/**
	 * Upgrade being executed.
	 */
	class OnUpgradeEvent extends UpgradeEvent {
		/**
		 * @var IUpgrade
		 */
		protected $upgrade;
		/**
		 * @var bool
		 */
		protected $suppress;

		/**
		 * @param IUpgradeManager $upgradeManager
		 * @param IUpgrade $upgrade
		 */
		public function __construct(IUpgradeManager $upgradeManager, IUpgrade $upgrade) {
			parent::__construct($upgradeManager);
			$this->upgrade = $upgrade;
			$this->suppress = false;
		}

		public function getUpgrade(): IUpgrade {
			return $this->upgrade;
		}

		public function suppress(bool $suppress = true) {
			$this->suppress = $suppress;
			return $this;
		}

		public function isSuppressed() {
			return $this->suppress;
		}
	}
