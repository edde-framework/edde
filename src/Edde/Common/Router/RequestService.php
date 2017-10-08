<?php
	declare(strict_types=1);
	namespace Edde\Common\Router;

	use Edde\Api\Protocol\Inject\ProtocolService;
	use Edde\Api\Router\IRequest;
	use Edde\Api\Router\IRequestService;
	use Edde\Api\Router\IResponse;
	use Edde\Common\Object\Object;

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
