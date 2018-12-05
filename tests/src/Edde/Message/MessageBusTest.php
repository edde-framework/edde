<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Container\ContainerException;
	use Edde\Service\Message\MessageBus;
	use Edde\TestCase;

	class MessageBusTest extends TestCase {
		use MessageBus;

		public function testCanHandle() {
			self::assertFalse($this->messageBus->canHandle($this->messageBus->createMessage('cannot-handle-this-one', 'common')));
		}

		/**
		 * @throws MessageException
		 */
		public function testResolveException() {
			$this->expectException(MessageException::class);
			$this->expectExceptionMessage('Cannot resolve Message Handler for message [nope] uuid [uuid] for resource [boom].');
			$this->messageBus->resolve($this->messageBus->createMessage('nope', 'boom', 'uuid'));
		}

		/**
		 * @throws MessageException
		 * @throws ContainerException
		 */
//		public function testStateMessage() {
//			$request = $this->messageBus->createPacket();
//			$this->messageBus->register('state', $this->container->create(TestStateHandler::class));
//			$request->request($state = $this->messageBus->createMessage('state', 'test-resource', 'da-uuid'));
//			$response = $this->messageBus->packet($request);
//		}
	}
