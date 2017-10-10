<?php
	declare(strict_types=1);
	namespace Edde\Common\Router;

		use Edde\Api\Router\IRouter;
		use Edde\Api\Runtime\Inject\Runtime;
		use Edde\Common\Object\Object;
		use Edde\Common\Response\Response;

		abstract class AbstractRouter extends Object implements IRouter {
			use Runtime;

			public function isHttp(): bool {
				return $this->runtime->isConsoleMode() === false;
			}

			/**
			 * this method should prepare basic response with proper response type
			 *
			 * @return \Edde\Api\Response\IResponse
			 */
			public function createResponse(): \Edde\Api\Response\IResponse {
				return new Response();
			}
		}
