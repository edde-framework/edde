<?php
	declare(strict_types=1);
	namespace Edde\Pub\Rest\Job\Manager;

	use Edde\Controller\RestController;
	use Edde\Service\Job\JobQueue;

	class StatsController extends RestController {
		use JobQueue;

		public function actionGet() {
			$this->jsonResponse($this->jobQueue->stats())->execute();
		}
	}
