<?php
	declare(strict_types=1);
	namespace Edde\Router;

	use Edde\Element\IRequest;
	use Edde\Inject\Http\RequestService;
	use Edde\Inject\Runtime\Runtime;
	use Edde\Object;

	abstract class AbstractRouter extends Object implements IRouter {
		use RequestService;
		use Runtime;
		/** @var IRequest */
		protected $request;

		protected function isHttp(): bool {
			return $this->runtime->isConsoleMode() === false;
		}

		/**
		 * @return string[]
		 */
		protected function getTargets(): array {
			return $this->isHttp() ? $this->requestService->getHeaders()->getAccepts() : [];
		}
	}
