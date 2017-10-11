<?php
	declare(strict_types=1);
	namespace App\Common\Index;

		use Edde\Common\Object\Object;
		use Edde\Ext\Response\ResponseFactory;

		class IndexView extends Object {
			use ResponseFactory;

			public function actionIndex() {
				$this->json([
					'foo',
					'bar',
				]);
			}
		}
