<?php
	declare(strict_types=1);
	namespace Edde\Common\Router;

	use Edde\Api\Router\IResponse;
	use Edde\Api\Router\IResponseService;
	use Edde\Common\Object\Object;

	class ResponseService extends Object implements IResponseService {
		/**
		 * @inheritdoc
		 */
		public function execute(IResponse $response): IResponse {
		}
	}
