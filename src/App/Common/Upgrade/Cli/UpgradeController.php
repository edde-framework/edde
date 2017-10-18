<?php
	namespace App\Common\Upgrade\Cli;

		use Edde\Common\Object\Object;
		use Edde\Ext\Control\CliController;

		class UpgradeController extends Object {
			use CliController;

			/**
			 * @help run an upgrade to the given version or do full upgrade to the latest available version
			 */
			public function actionUpgrade() {
			}
		}
