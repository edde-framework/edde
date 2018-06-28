<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Edde;
	use Edde\Router\AbstractRouter;

	class TestService extends Edde implements IController {
		public function noResponse() {
		}

		public function response() {
			return 123;
		}
	}

	class TestRouter extends AbstractRouter {
		protected $method;

		public function __construct(string $method) {
			$this->method = $method;
		}

		/** @inheritdoc */
		public function canHandle(): bool {
			return true;
		}

		/** @inheritdoc */
		public function createRequest(): IRequest {
			return new Request(TestService::class, $this->method);
		}
	}
