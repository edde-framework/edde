<?php
	declare(strict_types=1);
	namespace App\Common\Index;

		use Edde\Common\Object\Object;
		use Edde\Ext\Content\ContentFactory;

		class IndexView extends Object {
			use ContentFactory;

			public function actionIndex() {
				return $this->json(['abc']);
			}
		}
