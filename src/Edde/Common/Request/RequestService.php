<?php
	declare(strict_types=1);
	namespace Edde\Common\Request;

		use Edde\Api\Protocol\Inject\ProtocolService;
		use Edde\Api\Request\IRequest;
		use Edde\Api\Request\IRequestService;
		use Edde\Api\Response\IResponse;
		use Edde\Common\Object\Object;
		use Edde\Common\Response\Response;

		class RequestService extends Object implements IRequestService {
			use ProtocolService;

			/**
			 * @inheritdoc
			 */
			public function execute(IRequest $request): IResponse {
				$element = $this->protocolService->execute($request->getElement());
				return new Response($request);
			}
		}
