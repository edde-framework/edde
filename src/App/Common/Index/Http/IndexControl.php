<?php
	declare(strict_types=1);
	namespace App\Common\Index\Http;

		use App\Common\Index\AbstractIndexControl;
		use Edde\Ext\Response\ResponseFactory;

		class IndexControl extends AbstractIndexControl {
			use ResponseFactory;

			public function actionIndex() {
				$this->sendScalar([
					'foo',
					'bar',
				]);
			}
		}
