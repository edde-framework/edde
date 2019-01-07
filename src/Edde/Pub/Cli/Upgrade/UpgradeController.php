<?php
	declare(strict_types=1);
	namespace Edde\Pub\Cli\Upgrade;

	use Edde\Controller\CliController;
	use Edde\Service\Upgrade\UpgradeManager;
	use Edde\Service\Upgrade\VersionService;
	use Edde\Upgrade\CurrentVersionException;
	use Edde\Upgrade\UpgradeException;

	class UpgradeController extends CliController {
		use UpgradeManager;
		use VersionService;

		/**
		 * @help run an upgrade to the given version or do full upgrade to the latest available version
		 * @help [--version] <version name>: select target upgrade
		 *
		 * @throws UpgradeException
		 */
		public function actionUpgrade() {
			try {
				printf("Upgraded to [%s].\n", $this->upgradeManager->upgrade()->getVersion());
			} catch (CurrentVersionException $exception) {
				printf("Everything is nice and shiny on version [%s]!\n", $this->versionService->getVersion());
				echo sprintf("Installed upgrades:\n");
				foreach ($this->versionService->getCollection() as $upgrade) {
					echo sprintf("\t - [%s] on [%s]\n", $upgrade['version'], $upgrade['stamp']->format('Y-m-d H:i:s.u'));
				}
			}
		}

		/**
		 * @help list currently installed upgrades
		 */
		public function actionList() {
			printf("List of currently installed upgrades:\n");
			foreach ($this->versionService->getCollection() as $upgrade) {
				printf("\t - version [%s] from [%s]\n", $upgrade['version'], $upgrade['stamp']->format('Y-m-d H:i:s.u'));
			}
		}
	}
