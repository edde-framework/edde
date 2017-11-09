<?php
	namespace App\Common\Upgrade\Http;

		use Edde\Api\Upgrade\Inject\UpgradeManager;
		use Edde\Ext\Control\HttpController;

		class UpgradeController {
			use HttpController;
			use UpgradeManager;

			public function actionVersion() {
				echo $this->upgradeManager->getVersion();
			}
		}
