<?php
	declare(strict_types=1);
	namespace Edde\Common\Router;

		use Edde\Api\Router\IResponse;
		use Edde\Api\Router\IRouter;
		use Edde\Api\Runtime\Inject\Runtime;
		use Edde\Common\Object\Object;

		abstract class AbstractRouter extends Object implements IRouter {
			use Runtime;

			public function isHttp(): bool {
				return $this->runtime->isConsoleMode() === false;
			}

			/**
			 * this method should prepare basic response with proper response type
			 *
			 * @return IResponse
			 */
			public function createResponse(): IResponse {
				return new Response();
			}
		}
