<?php
	declare(strict_types=1);
	namespace App\Common\Protocol\Rest;

		use App\Common\Protocol\AbstractProtocolController;
		use Edde\Ext\Control\RestController;

		class ProtocolController extends AbstractProtocolController {
			use RestController;

			public function actionPost() {
			}
		}
