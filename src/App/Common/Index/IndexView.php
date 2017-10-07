<?php
	declare(strict_types=1);
	namespace App\Common\Index;

	use Edde\Common\Object\Object;

	class IndexView extends Object {
		public function actionIndex() {
			echo 'hello!';
		}
	}
