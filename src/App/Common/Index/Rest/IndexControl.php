<?php
	namespace App\Common\Index\Rest;

		use App\Common\Index\AbstractIndexControl;
		use Edde\Api\Response\Inject\ResponseService;
		use Edde\Common\Content\IterableContent;
		use Edde\Common\Response\Response;

		/**
		 * Rest like view is able to handle standard http method mapping into individual actions,
		 * for example actionGet, actionPost, .... Content negotiation is working too.
		 */
		class IndexControl extends AbstractIndexControl {
			use ResponseService;

			public function actionGet() {
				$this->responseService->execute(new Response(new IterableContent(function () {
					yield 'yumiii!';
				})));
			}
		}
