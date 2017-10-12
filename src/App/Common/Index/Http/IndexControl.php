<?php
	declare(strict_types=1);
	namespace App\Common\Index\Http;

		use App\Common\Index\AbstractIndexControl;

		class IndexControl extends AbstractIndexControl {
			public function actionIndex() {
				echo 'yummi!';
			}
		}
