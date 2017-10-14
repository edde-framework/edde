<?php
	namespace App\Common\Index\Rest;

		use App\Common\Index\AbstractIndexController;
		use Edde\Api\Http\IResponse;
		use Edde\Api\Log\Inject\LogService;
		use Edde\Ext\Control\RestController;

		/**
		 * Rest like view is able to handle standard http method mapping into individual actions,
		 * for example actionGet, actionPost, .... Content negotiation is working too.
		 */
		class IndexController extends AbstractIndexController {
			use RestController;
			use LogService;

			public function actionGet() {
				$this->json('Hello from here!');
			}

			public function actionPost() {
				try {
					if (rand(0, 100) > 25) {
						$this->logService->error('oou, there is something really not nice!', ['stderr']);
						throw new \Exception('kaboom');
					}
					$this->json('tasty cake was made just for you!', IResponse::R200_OK_CREATED);
				} catch (\Throwable $throwable) {
					$this->json('ooou, owen is broken!', IResponse::R400_BAD_REQUEST);
				}
			}
		}
