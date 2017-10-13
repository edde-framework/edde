<?php
	namespace App\Common\Index\Rest;

		use App\Common\Index\AbstractIndexControl;
		use Edde\Api\Http\Inject\ResponseService;
		use Edde\Common\Content\CallableContent;
		use Edde\Common\Http\Cookies;
		use Edde\Common\Http\Headers;
		use Edde\Common\Http\Response;

		/**
		 * Rest like view is able to handle standard http method mapping into individual actions,
		 * for example actionGet, actionPost, .... Content negotiation is working too.
		 */
		class IndexControl extends AbstractIndexControl {
			use ResponseService;

			public function actionGet() {
				$this->responseService->execute(new Response(new CallableContent(function () {
					yield 'yumiii!';
				}), new Headers(), new Cookies()));
			}
		}
