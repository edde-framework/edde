<?php
	declare(strict_types=1);
	namespace App\Common\Index\Http;

		use App\Common\Index\AbstractIndexController;
		use Edde\Ext\Control\HttpController;

		class IndexController extends AbstractIndexController {
			use HttpController;

			public function actionIndex() {
			}
		}
