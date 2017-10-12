<?php
	declare(strict_types=1);
	namespace Edde\Common\Router;

		use Edde\Api\Element\IElement;
		use Edde\Api\Http\Inject\HttpService;
		use Edde\Api\Request\IRequest;
		use Edde\Api\Router\IRouter;
		use Edde\Api\Runtime\Inject\Runtime;
		use Edde\Common\Element\Message;
		use Edde\Common\Object\Object;

		abstract class AbstractRouter extends Object implements IRouter {
			use HttpService;
			use Runtime;
			/**
			 * @var IRequest
			 */
			protected $request;

			public function isHttp() : bool {
				return $this->runtime->isConsoleMode() === false;
			}

			public function getTargetList() : array {
				return $this->isHttp() ? $this->httpService->getRequest()->getHeaderList()->getAcceptList() : [];
			}

			protected function createElement(string $request, array $parameterList) : IElement {
				$message = new Message($request);
				$message->mergeAttributeList($parameterList);
				return $message;
			}
		}
