<?php
	namespace App\Common\Index\Cli;

		use App\Common\Index\AbstractIndexControl;
		use Edde\Ext\Control\CliControl;

		class IndexControl extends AbstractIndexControl {
			use CliControl;

			public function actionIndex() {
				echo "hello from a CLI control!";
			}
		}
