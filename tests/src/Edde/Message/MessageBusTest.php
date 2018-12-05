<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Service\Message\MessageBus;
	use Edde\TestCase;

	class MessageBusTest extends TestCase {
		use MessageBus;

		public function testStateMessage() {
			$request = $this->messageBus->createPacket();
			$request->request($state = $this->messageBus->createMessage('state', 'test-resource', 'da-uuid'));
			$response = $this->messageBus->packet($request);
		}
	}
