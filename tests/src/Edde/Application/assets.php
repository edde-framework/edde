<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Edde;
	use Edde\Router\AbstractRouter;
	use function array_merge;

	class TestContext extends AbstractContext {
		public function cascade(string $delimiter, string $name = null): array {
			return array_merge(parent::cascade($delimiter, $name), [
				'Foo' . $delimiter . 'Bar' . ($name ? $delimiter . $name : ''),
				'Bar' . $delimiter . 'Foo' . ($name ? $delimiter . $name : ''),
			]);
		}
	}

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
