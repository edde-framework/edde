<?php
	declare(strict_types=1);
	namespace Edde\Router;

	use Edde\Autowire;
	use Edde\Element\IRequest;
	use Edde\Obj3ct;
	use Edde\Service\Http\RequestService;
	use Edde\Service\Runtime\Runtime;

	abstract class AbstractRouter extends Obj3ct implements IRouter {
		use RequestService;
		use Runtime;
		use Autowire;
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
