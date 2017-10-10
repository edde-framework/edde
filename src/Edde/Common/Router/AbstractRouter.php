<?php
	declare(strict_types=1);
	namespace Edde\Common\Router;

		use Edde\Api\Http\Inject\HttpService;
		use Edde\Api\Router\IRouter;
		use Edde\Api\Runtime\Inject\Runtime;
		use Edde\Common\Object\Object;

		abstract class AbstractRouter extends Object implements IRouter {
			use HttpService;
			use Runtime;

			public function isHttp(): bool {
				return $this->runtime->isConsoleMode() === false;
			}

			public function getTargetList(): array {
				if ($this->isHttp()) {
//					$this->httpService->getRequest()->get
				}
				return [];
			}
		}
