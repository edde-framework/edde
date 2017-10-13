<?php
	namespace App\Common\Index\Rest;

		use App\Common\Index\AbstractIndexControl;
		use Edde\Ext\Control\HttpControl;

		/**
		 * Rest like view is able to handle standard http method mapping into individual actions,
		 * for example actionGet, actionPost, .... Content negotiation is working too.
		 */
		class IndexControl extends AbstractIndexControl {
			use HttpControl;

			public function actionGet() {
				$this->send(function () {
					yield json_encode('yumiii!');
				});
			}
		}
