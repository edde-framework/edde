<?php
	declare(strict_types=1);
	namespace Edde\Cli\Upgrade;

	use Edde\Application\CliController;
	use Edde\Application\IController;
	use Edde\Edde;
	use Edde\Service\Upgrade\UpgradeManager;
	use Edde\Upgrade\CurrentVersionException;

	class UpgradeController extends Edde implements IController {
		use CliController;
		use UpgradeManager;

		/**
		 * @help run an upgrade to the given version or do full upgrade to the latest available version
		 * @help [--version] <version name>: select target upgrade
		 */
		public function actionUpgrade() {
			try {
				printf("Upgraded to [%s].\n", $this->upgradeManager->upgrade()->getVersion());
			} catch (CurrentVersionException $exception) {
				printf("Everything is nice and shiny on version [%s]!\n", $this->upgradeManager->getVersion());
				echo sprintf("Installed upgrades:\n");
				foreach ($this->upgradeManager->getCurrentCollection()->orderDesc('u.stamp')->exe as $record) {
					$entity = $record->getEntity('u');
					echo sprintf("\t - [%s] on [%s]\n", $entity->get('version'), $entity->get('stamp')->format('Y-m-d H:i:s.u'));
				}
			}
		}

		/**
		 * @help list currently installed upgrades
		 */
		public function actionList() {
			printf("List of currently installed upgrades:\n");
			foreach ($this->upgradeManager->getCurrentCollection()->orderDesc('u.stamp') as $record) {
				$entity = $record->getEntity('u');
				printf("\t - version [%s] from [%s]\n", $entity->get('version'), $entity->get('stamp')->format('Y-m-d H:i:s'));
			}
		}
	}
