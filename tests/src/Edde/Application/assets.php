<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Configurable\AbstractConfigurator;
	use Edde\Controller\AbstractController;
	use Edde\Controller\IController;
	use Edde\Edde;
	use Edde\Runtime\IRuntime;
	use Edde\Service\Log\LogService;

	class TestMagicService extends AbstractController {
		use LogService;

		public function doMagick() {
			$this->logService->info('fireball!');
			return 'fireball!';
		}
	}

	class SomeRouterServiceConfigurator extends AbstractConfigurator {
		/** @var $instance IRouterService */
		public function configure($instance) {
			parent::configure($instance);
			$instance->default(new Request(TestMagicService::class, 'doMagick'));
		}
	}

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

	class TestWrongControllerRouter extends AbstractRouterService implements IRouterService {
		/** @inheritdoc */
		public function canHandle(): bool {
			return true;
		}

		/** @inheritdoc */
		public function request(): IRequest {
			return new Request(SomeService::class, 'nope');
		}
	}

	class TestRouterService extends AbstractRouterService implements IRouterService {
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
		public function request(): IRequest {
			return $this->request ?: $this->request = new Request(TestService::class, $this->method);
		}
	}
