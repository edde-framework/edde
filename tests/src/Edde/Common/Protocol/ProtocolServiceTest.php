<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\Inject\ProtocolService;
	use Edde\Common\Request\Message;
	use Edde\Ext\Test\TestCase;

	require_once __DIR__ . '/../assets/assets.php';

	class ProtocolServiceTest extends TestCase {
		use ProtocolService;

		public function testCanHandle() {
			self::assertTrue($this->protocolService->canHandle(new Message('foo.foo-service/foo-action')));
		}

		public function testSimpleExecute() {
			$this->protocolService->execute($message = new Message('foo.foo-service/foo-action'));
			self::assertTrue($message->getMeta('done'));
		}
	}
