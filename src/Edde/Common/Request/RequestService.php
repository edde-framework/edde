<?php
	declare(strict_types=1);
	namespace Edde\Common\Request;

		use Edde\Api\Protocol\Inject\ProtocolService;
		use Edde\Api\Request\IRequest;
		use Edde\Api\Request\IRequestService;
		use Edde\Api\Response\IResponse;
		use Edde\Api\Router\Inject\RouterService;
		use Edde\Common\Object\Object;
		use Edde\Common\Response\Response;

		class RequestService extends Object implements IRequestService {
			use ProtocolService;
			use RouterService;
			/**
			 * @var IRequest
			 */
			protected $request;

			/**
			 * @inheritdoc
			 */
			public function execute() : IResponse {
				return $this->run($this->getRequest());
			}

			/**
			 * @inheritdoc
			 */
			public function run(IRequest $request) : IResponse {
				$this->request = $request;
				$this->protocolService->execute($request->getElement());
				return new Response($request);
			}

			/**
			 * @inheritdoc
			 */
			public function getRequest() : IRequest {
				if ($this->request) {
					return $this->request;
				}
				return $this->request = $this->routerService->createRequest();
			}
		}
