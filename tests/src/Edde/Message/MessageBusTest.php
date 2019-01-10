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
			$this->expectExceptionMessage('Cannot resolve Message Service for message [nope] for target [boom]; please register one of [Pub\BoomMessageService, Pub\Boom, Pub\Message\NopeMessageService, Pub\Message\CommonMessageService] (Edde\Message\IMessageService).');
			$this->messageBus->resolve($this->messageBus->createMessage('nope', 'boom'));
		}

		/**
		 * @throws MessageException
		 */
		public function testResolveInterfaceException() {
			$this->expectException(MessageException::class);
			$this->expectExceptionMessage('Cannot resolve Message Service for message [dummy] for target [- no target -]; please register one of [Pub\Message\DummyMessageService, Pub\Message\CommonMessageService] (Edde\Message\IMessageService).');
			$this->messageBus->resolve($this->messageBus->createMessage('dummy'));
		}

		/**
		 * @throws MessageException
		 */
		public function testMissingMethodException() {
			$response = $this->messageBus->packet($this->messageBus->createPacket()->message($input = $this->messageBus->createMessage('kaboom', 'common-message-service')));
			self::assertInstanceOf(IPacket::class, $response);
			self::assertCount(1, $response->messages());
			[$message] = $response->messages();
			self::assertEquals([
				'message' => $input->export(),
				'text'    => 'Cannot handle message [kaboom] in [Edde\Pub\Message\CommonMessageService]. Please implement Edde\Pub\Message\CommonMessageService::onKaboomMessage($message, $packet) method.',
			], $message->getAttrs());
		}

		/**
		 * @throws MessageException
		 */
		public function testStateMessage() {
			$input = $this->messageBus->createPacket();
			$input->message($state = $this->messageBus->createMessage('state', 'edde.message.common-message-service', null));
			$output = $this->messageBus->packet($input);
			self::assertInstanceOf(IPacket::class, $output);
			self::assertCount(1, $output->messages());
			[$pull] = $output->messages();
			self::assertSame(['foo' => 'bar'], $pull->getAttrs());
		}
	}
