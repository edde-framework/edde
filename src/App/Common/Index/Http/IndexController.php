<?php
	declare(strict_types=1);
	namespace App\Common\Index\Http;

		use App\Common\Index\AbstractIndexController;
		use Edde\Api\Assets\Inject\AssetsDirectory;
		use Edde\Ext\Control\HttpController;

		class IndexController extends AbstractIndexController {
			use HttpController;
			use AssetsDirectory;

			public function actionIndex() {
				$this->html($this->assetsDirectory->directory('templates')->file('index.html'));
			}
		}
