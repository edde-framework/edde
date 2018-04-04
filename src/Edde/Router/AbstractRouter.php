<?php
	declare(strict_types=1);
	namespace Edde\Router;

	use Edde\Edde;
	use Edde\Element\IRequest;
	use Edde\Service\Http\RequestService;
	use Edde\Service\Runtime\Runtime;

	abstract class AbstractRouter extends Edde implements IRouter {
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
