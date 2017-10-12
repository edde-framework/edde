<?php
	declare(strict_types=1);
	namespace Edde\Common\Router;

		use Edde\Api\Element\IElement;
		use Edde\Api\Http\Inject\HttpService;
		use Edde\Api\Request\IRequest;
		use Edde\Api\Router\Exception\BadRequestException;
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

			/**
			 * @inheritdoc
			 */
			public function createRequest() : IRequest {
				if ($this->request) {
					return $this->request;
				}
				return $this->request = ($this->isHttp() ? $this->createHttpRequest() : $this->createCliRequest());
			}

			public function isHttp() : bool {
				return $this->runtime->isConsoleMode() === false;
			}

			public function getTargetList() : array {
				if ($this->isHttp()) {
					return $this->httpService->getRequest()->getHeaderList()->getAcceptList();
				}
				return [];
			}

			protected function createElement(string $request, array $parameterList) : IElement {
				$message = new Message($request);
				$message->mergeAttributeList($parameterList);
				return $message;
			}

			/**
			 * @return IRequest
			 * @throws BadRequestException
			 */
			protected function createHttpRequest() : IRequest {
				throw new BadRequestException(sprintf('Http request is not supported on router [%s]', static::class));
			}

			/**
			 * @return IRequest
			 * @throws BadRequestException
			 */
			protected function createCliRequest() : IRequest {
				throw new BadRequestException(sprintf('Cli request is not supported on router [%s]', static::class));
			}
		}
