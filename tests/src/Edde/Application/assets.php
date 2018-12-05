<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Controller\IController;
	use Edde\Edde;
	use Edde\Runtime\IRuntime;

	class DummyRuntime implements IRuntime {
		public function isConsoleMode(): bool {
			return true;
		}

		public function getArguments(): array {
			return [];
		}
	}

	class TestService extends Edde implements IController {
		public function noResponse() {
		}

		public function response() {
			return 123;
		}
	}

	class SomeService extends Edde {
	}

	class TestWrongControllerRouter extends Edde implements IRouterService {
		/** @inheritdoc */
		public function canHandle(): bool {
			return true;
		}

		/** @inheritdoc */
		public function createRequest(): IRequest {
			return new Request(SomeService::class, 'nope');
		}
	}

	class TestRouterService extends Edde implements IRouterService {
		protected $method;
		protected $request;

		public function __construct(string $method) {
			$this->method = $method;
		}

		/** @inheritdoc */
		public function canHandle(): bool {
			return true;
		}

		/** @inheritdoc */
		public function createRequest(): IRequest {
			return $this->request ?: $this->request = new Request(TestService::class, $this->method);
		}
	}
