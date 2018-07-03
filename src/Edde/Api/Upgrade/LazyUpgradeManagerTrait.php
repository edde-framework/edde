<?php
	declare(strict_types=1);

	namespace Edde\Api\Upgrade;

	/**
	 * Lazy upgrade manager dependency.
	 */
	trait LazyUpgradeManagerTrait {
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
