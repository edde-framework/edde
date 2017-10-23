<?php
	namespace App\Common\Upgrade\Cli;

		use Edde\Api\Application\Exception\AbortException;
		use Edde\Api\Upgrade\Exception\CurrentVersionException;
		use Edde\Api\Upgrade\Inject\UpgradeManager;
		use Edde\Common\Object\Object;
		use Edde\Ext\Control\CliController;

		class UpgradeController extends Object {
			use CliController;
			use UpgradeManager;

			/**
			 * @help run an upgrade to the given version or do full upgrade to the latest available version
			 * @help [--version] <version name>: select target upgrade
			 *
			 * @throws AbortException
			 */
			public function actionUpgrade() {
				try {
					printf("Upgraded to [%s].\n", $this->upgradeManager->upgrade()->getVersion());
				} catch (CurrentVersionException $exception) {
					printf("Everything is nice and shiny!\n");
				} catch (\Throwable $exception) {
					$this->abort($exception->getMessage(), -1, $exception);
				}
			}

			/**
			 * @help list currently installed upgrades
			 */
			public function actionList() {
				printf("List of currently installed upgrades:\n");
				foreach ($this->upgradeManager->getCurrentList() as $upgrade) {
					printf("\t - version [%s] from [%s]\n", $upgrade->get('version'), date('Y-m-d H:i:s', $upgrade->get('stamp')));
				}
			}

			/**
			 * @help run an rollback to the given version or to the initial state of an application
			 * @help this is very dangerous command, so use it wisely!
			 * @help --yes=im-totally-sure: this parameter must be present to do the downgrade
			 * @help [--version] <version name>: select target (downgrade) upgrade
			 */
			public function actionRollback() {
			}
		}
