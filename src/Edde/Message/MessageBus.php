<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Service\Security\RandomService;

	class MessageBus extends AbstractMessageHandler implements IMessageBus {
		use RandomService;

		/** @inheritdoc */
		public function packet(IPacket $packet): IPacket {
			$response = $this->createPacket();
			foreach ($packet->requests() as $message) {
				$response->response($this->request($message));
			}
			return $response;
		}

		/** @inheritdoc */
		public function createPacket(): IPacket {
			return new Packet(self::VERSION, $this->randomService->uuid());
		}

		/** @inheritdoc */
		public function createMessage(string $type, string $resource, string $uuid = null): IMessage {
			return new Message($type, $resource, $uuid ?: $this->randomService->uuid());
		}
	}
