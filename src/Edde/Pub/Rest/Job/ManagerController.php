<?php
	declare(strict_types=1);
	namespace Edde\Pub\Rest\Job;

	use Edde\Controller\RestController;
	use Edde\Service\Job\JobQueue;

	class ManagerController extends RestController {
		use JobQueue;

		public function actionCleanup() {
			$this->jobQueue->cleanup();
			$this->jsonResponse('ok')->execute();
		}
	}
