<?php
	namespace App\Common\Index\Rest;

		use App\Common\Index\AbstractIndexController;
		use Edde\Ext\Control\RestController;

		/**
		 * Rest like view is able to handle standard http method mapping into individual actions,
		 * for example actionGet, actionPost, .... Content negotiation is working too.
		 */
		class IndexController extends AbstractIndexController {
			use RestController;

			public function actionGet() {
//				'tasty cake was made just for you!'
			}
		}
