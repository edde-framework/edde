<?php
	declare(strict_types=1);
	namespace Edde\Common\Router;

	use Edde\Exception\Http\NoHttpException;
	use Edde\Inject\Http\RequestService;
	use Edde\Inject\Runtime\Runtime;
	use Edde\Object;
	use Edde\Router\IRouter;

	abstract class AbstractRouter extends Object implements IRouter {
		use RequestService;
		use Runtime;
		/** @var \Edde\Element\IRequest */
		protected $request;

		protected function isHttp(): bool {
			return $this->runtime->isConsoleMode() === false;
		}

		/**
		 * @return string[]
		 * @throws NoHttpException
		 */
		protected function getTargets(): array {
			return $this->isHttp() ? $this->requestService->getHeaders()->getAccepts() : [];
		}
	}
