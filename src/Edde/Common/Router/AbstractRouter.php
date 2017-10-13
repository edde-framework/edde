<?php
	declare(strict_types=1);
	namespace Edde\Common\Router;

		use Edde\Api\Element\IElement;
		use Edde\Api\Http\Exception\NoHttpException;
		use Edde\Api\Http\Inject\RequestService;
		use Edde\Api\Request\IRequest;
		use Edde\Api\Router\IRouter;
		use Edde\Api\Runtime\Inject\Runtime;
		use Edde\Common\Element\Message;
		use Edde\Common\Object\Object;

		abstract class AbstractRouter extends Object implements IRouter {
			use RequestService;
			use Runtime;
			/**
			 * @var IRequest
			 */
			protected $request;

			protected function isHttp(): bool {
				return $this->runtime->isConsoleMode() === false;
			}

			/**
			 * @return string[]
			 * @throws NoHttpException
			 */
			protected function getTargetList(): array {
				return $this->isHttp() ? $this->requestService->getHeaders()->getAcceptList() : [];
			}

			protected function createElement(string $request, array $parameterList): IElement {
				$message = new Message($request);
				$message->mergeAttributeList($parameterList);
				return $message;
			}
		}
