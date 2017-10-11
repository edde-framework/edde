<?php
	namespace App\Common\Protocol\Rest;

		use App\Common\Protocol\AbstractProtocolControl;
		use Edde\Ext\Response\ResponseFactory;

		class ProtocolControl extends AbstractProtocolControl {
			use ResponseFactory;

			public function actionGet() {
				$this->sendScalar([
					'hello',
					'the protocol',
					'is',
					'here!!',
				]);
			}
		}
