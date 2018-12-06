<?php
	namespace Edde\Message;

	class TestStateHandler extends AbstractMessageHandler {
		/** @inheritdoc */
		public function canHandle(IMessage $message): bool {
			return $message->getType() === 'state';
		}

		/** @inheritdoc */
		public function push(IMessage $message, IPacket $packet): IMessageHandler {
			$packet->pull($this->reply($message, [
				'foo' => 'bar',
			]));
		}

		/** @inheritdoc */
		public function pull(IMessage $message, IPacket $packet): IMessageHandler {
		}
	}
