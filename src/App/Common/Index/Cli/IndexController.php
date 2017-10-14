<?php
	namespace App\Common\Index\Cli;

		use App\Common\Index\AbstractIndexController;
		use Edde\Ext\Control\CliControl;

		class IndexController extends AbstractIndexController {
			use CliControl;

			public function actionIndex() {
				echo "hello from a CLI control!";
			}
		}
