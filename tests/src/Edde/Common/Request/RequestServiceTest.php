<?php
	declare(strict_types=1);

	namespace Edde\Common\Request;

	use Edde\Api\Request\Inject\RequestService;
	use Edde\Ext\Test\TestCase;

	require_once __DIR__ . '/../assets/assets.php';

	class RequestServiceTest extends TestCase {
		use RequestService;

		public function testCanHandle() {
			self::assertFalse($this->requestService->canHandle(new Message('bar.bar-service/bar-action')), 'canHandle() is handling unavailable service!');
			self::assertTrue($this->requestService->canHandle(new Message('foo.foo-service/foo-action')), 'canHandle() is NOT handling available service!');
		}
	}
