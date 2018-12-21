<?php
	declare(strict_types=1);
	namespace Edde\Cli\Tick;

	use Edde\Controller\CliController;
	use Edde\Service\Tick\TickService;

	class TickController extends CliController {
		use TickService;

		public function actionTick() {
			$this->tickService->tick();
		}
	}
