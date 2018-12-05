<?php
	namespace Edde\Message;

	class TestStateHandler extends AbstractMessageHandler {
		/** @inheritdoc */
		public function canHandle(IMessage $message): bool {
			return $message->getType() === 'state';
		}

		/** @inheritdoc */
		public function request(IMessage $message): IMessage {
			return $this->reply($message, [
				'foo' => 'bar',
			]);
		}

		/** @inheritdoc */
		public function response(IMessage $message): IMessage {
		}
	}
