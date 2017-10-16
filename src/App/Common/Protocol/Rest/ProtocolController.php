<?php
	namespace App\Common\Protocol\Rest;

		use App\Common\Protocol\AbstractProtocolController;
		use Edde\Ext\Control\HttpController;

		class ProtocolController extends AbstractProtocolController {
			use HttpController;

			public function actionGet() {
			}
		}
