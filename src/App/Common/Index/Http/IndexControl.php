<?php
	declare(strict_types=1);
	namespace App\Common\Index\Http;

		use App\Common\Index\AbstractIndexControl;
		use Edde\Ext\Control\HttpControl;

		class IndexControl extends AbstractIndexControl {
			use HttpControl;

			public function actionIndex() {
				$this->send(function () {
					yield 'yumiii!';
				}, 'text/html');
			}
		}
