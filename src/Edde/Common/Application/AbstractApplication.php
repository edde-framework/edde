<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Router\LazyRouterServiceTrait;
	use Edde\Common\Object;

	/**
	 * Common implementation for all applications.
	 */
	abstract class AbstractApplication extends Object implements IApplication {
		use LazyProtocolServiceTrait;
		use LazyRouterServiceTrait;
		use LazyResponseManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function run() {
			$this->protocolService->execute($this->routerService->createRequest());
			$this->responseManager->execute();
		}
	}
