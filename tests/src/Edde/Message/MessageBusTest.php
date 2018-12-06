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
			$this->expectExceptionMessage('Cannot resolve Message Handler for message [nope] uuid [uuid] for namespace [boom]; please register a service [Boom\NopeMessageHandler] (IMessageHandler).');
			$this->messageBus->resolve($this->messageBus->createMessage('nope', 'boom', 'uuid'));
		}

		/**
		 * @throws MessageException
		 */
		public function testResolveInterfaceException() {
			$this->expectException(MessageException::class);
			$this->expectExceptionMessage('Message handler service [Edde\Message\DummyMessageHandler] does not implement interface [Edde\Message\IMessageHandler].');
			$this->messageBus->resolve($this->messageBus->createMessage('dummy', 'edde.message', 'da-uuid'));
		}

		/**
		 * @throws MessageException
		 */
		public function testStateMessage() {
			$input = $this->messageBus->createPacket();
			$input->push($state = $this->messageBus->createMessage('state', 'edde.message', 'da-uuid'));
			$output = $this->messageBus->packet($input);
			self::assertInstanceOf(IPacket::class, $output);
			self::assertCount(1, $output->pulls());
			[$pull] = $output->pulls();
			self::assertSame(['foo' => 'bar'], $pull->getAttrs());
		}
	}
