<?php
	namespace App\Common\Index\Cli;

		use App\Common\Index\AbstractIndexController;
		use Edde\Ext\Control\CliController;

		class IndexController extends AbstractIndexController {
			use CliController;

			public function actionIndex() {
				echo "hello from a CLI control!";
			}
		}
