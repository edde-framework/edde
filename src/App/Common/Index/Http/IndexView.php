<?php
	declare(strict_types=1);
	namespace App\Common\Index\Http;

		use App\Common\Index\AbstractIndexView;
		use Edde\Ext\Response\ResponseFactory;

		class IndexView extends AbstractIndexView {
			use ResponseFactory;

			public function actionIndex() {
				$this->sendScalar([
					'foo',
					'bar',
				]);
			}
		}
