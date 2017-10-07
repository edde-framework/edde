<?php
	declare(strict_types=1);
	namespace Edde\Common\Router;

	use Edde\Api\Router\IRequest;
	use Edde\Api\Router\IRequestService;
	use Edde\Api\Router\IResponse;
	use Edde\Common\Object\Object;

	class RequestService extends Object implements IRequestService {
		/**
		 * @inheritdoc
		 */
		public function execute(IRequest $request): IResponse {
		}
	}
