<?php
	namespace App\Common\Index\Rest;

		use App\Common\Index\AbstractIndexControl;

		/**
		 * Rest like view is able to handle standard http method mapping into individual actions,
		 * for example actionGet, actionPost, .... Content negotiation is working too.
		 */
		class IndexControl extends AbstractIndexControl {
			public function actionGet() {
				echo json_encode('yumiii!');
			}
		}
