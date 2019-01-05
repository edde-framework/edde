<?php
	declare(strict_types=1);
	namespace Edde\Controller;

	use Edde\Edde;
	use Edde\Service\Application\RouterService;

	abstract class AbstractController extends Edde implements IController {
		use RouterService;

		protected function getParams(): array {
			return $this->routerService->request()->getParams();
		}
	}
