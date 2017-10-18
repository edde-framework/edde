<?php
	namespace App\Common\Upgrade\Cli;

		use Edde\Common\Object\Object;
		use Edde\Ext\Control\CliController;

		class UpgradeController extends Object {
			use CliController;

			/**
			 * @help run an upgrade to the given version or do full upgrade to the latest available version
			 * @help [--version] <version name>: select target upgrade
			 */
			public function actionUpgrade() {
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
