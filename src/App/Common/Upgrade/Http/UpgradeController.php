<?php
	namespace App\Common\Upgrade\Http;

		use Edde\Api\Upgrade\Inject\UpgradeManager;
		use Edde\Common\Object\Object;
		use Edde\Ext\Control\HttpController;

		class UpgradeController extends Object {
			use HttpController;
			use UpgradeManager;

			public function actionVersion() {
				header('Content-Type: text/plain');
				printf("Current version [%s]\n", $this->upgradeManager->getVersion());
				printf("Installed upgrades:\n");
				foreach ($this->upgradeManager->getCurrentList()->order('c.stamp', false) as $entity) {
					printf("\t - [%s] on [%s]\n", $entity->get('version'), $entity->get('stamp')->format('Y-m-d H:i:s.u'));
				}
			}
		}
