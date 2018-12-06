<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use function sprintf;

	class MessageBus extends AbstractMessageHandler implements IMessageBus {
		/** @var IMessageHandler[][] */
		protected $messageHandlers = [];

		/** @inheritdoc */
		public function register(string $type, IMessageHandler $messageHandler): IMessageBus {
			$this->messageHandlers[$type][] = $messageHandler;
			return $this;
		}

		/** @inheritdoc */
		public function packet(IPacket $packet): IPacket {
			$response = $this->createPacket();
			foreach ($packet->pushes() as $message) {
				$response->pull($this->push($message));
			}
			return $response;
		}

		/** @inheritdoc */
		public function resolve(IMessage $message): IMessageHandler {
			foreach ($this->messageHandlers[$message->getType()] ?? [] as $messageHandler) {
				if ($messageHandler->canHandle($message)) {
					return $messageHandler;
				}
			}
			throw new MessageException(sprintf('Cannot resolve Message Handler for message [%s] uuid [%s] for resource [%s].', $message->getType(), $message->getUuid(), $message->getResource()));
		}

		/** @inheritdoc */
		public function createPacket(): IPacket {
			return new Packet(self::VERSION, $this->randomService->uuid());
		}

		/** @inheritdoc */
		public function canHandle(IMessage $message): bool {
			foreach ($this->messageHandlers[$message->getType()] ?? [] as $messageHandler) {
				if ($messageHandler->canHandle($message)) {
					return true;
				}
			}
			return false;
		}

		/** @inheritdoc */
		public function push(IMessage $message, IPacket $packet): IMessageHandler {
			$this->resolve($message)->push($message, $packet);
			return $this;
		}

		/** @inheritdoc */
		public function pull(IMessage $message, IPacket $packet): IMessageHandler {
			$this->resolve($message)->pull($message, $packet);
			return $this;
		}
	}
