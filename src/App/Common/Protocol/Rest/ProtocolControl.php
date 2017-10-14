<?php
	namespace App\Common\Protocol\Rest;

		use App\Common\Protocol\AbstractProtocolControl;

		class ProtocolControl extends AbstractProtocolControl {
			public function actionGet() {
				echo json_encode([
					'hello',
					'the protocol',
					'is',
					'here!!',
				]);
			}
		}
