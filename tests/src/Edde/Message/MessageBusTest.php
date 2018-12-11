<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Service\Message\MessageBus;
	use Edde\TestCase;

	class MessageBusTest extends TestCase {
		use MessageBus;

		/**
		 * @throws MessageException
		 */
		public function testResolveException() {
			$this->expectException(MessageException::class);
			$this->expectExceptionMessage('Cannot resolve Message Service for message [nope] uuid [uuid] for namespace [boom]; please register a service [Boom] (Edde\Message\IMessageService).');
			$this->messageBus->resolve($this->messageBus->createMessage('boom', 'nope', null, 'uuid'));
		}

		/**
		 * @throws MessageException
		 */
		public function testResolveInterfaceException() {
			$this->expectException(MessageException::class);
			$this->expectExceptionMessage('Cannot resolve Message Service for message [dummy] uuid [da-uuid] for namespace [edde.message]; please register a service [Edde\Message] (Edde\Message\IMessageService).');
			$this->messageBus->resolve($this->messageBus->createMessage('edde.message', 'dummy', null, 'da-uuid'));
		}

		/**
		 * @throws MessageException
		 */
		public function testMissingMethodException() {
			$response = $this->messageBus->packet($this->messageBus->createPacket()->message($input = $this->messageBus->createMessage('edde.message.common-message-service', 'kaboom')));
			self::assertInstanceOf(IPacket::class, $response);
			self::assertCount(1, $response->messages());
			[$message] = $response->messages();
			self::assertEquals([
				'message' => $input->export(),
				'text'    => 'Cannot handle message [kaboom] in [Edde\Message\CommonMessageService]. Please implement Edde\Message\CommonMessageService::onKaboomMessage($message, $packet) method.',
			], $message->getAttrs());
		}

		/**
		 * @throws MessageException
		 */
		public function testStateMessage() {
			$input = $this->messageBus->createPacket();
			$input->message($state = $this->messageBus->createMessage('edde.message.common-message-service', 'state', null, 'da-uuid'));
			$output = $this->messageBus->packet($input);
			self::assertInstanceOf(IPacket::class, $output);
			self::assertCount(1, $output->messages());
			[$pull] = $output->messages();
			self::assertSame(['foo' => 'bar'], $pull->getAttrs());
		}
	}
