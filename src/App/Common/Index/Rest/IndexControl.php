<?php
	namespace App\Common\Index\Rest;

		use App\Common\Index\AbstractIndexControl;
		use Edde\Api\Http\IResponse;
		use Edde\Common\Content\JsonContent;
		use Edde\Common\Http\Response;
		use Edde\Ext\Control\HttpControl;

		/**
		 * Rest like view is able to handle standard http method mapping into individual actions,
		 * for example actionGet, actionPost, .... Content negotiation is working too.
		 */
		class IndexControl extends AbstractIndexControl {
			use HttpControl;

			public function actionGet() {
				(new Response(new JsonContent(json_encode('tasty cake was made just for you!'))))->setCode(IResponse::R200_OK_CREATED)->execute();
			}
		}
