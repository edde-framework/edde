<?php
	namespace Edde\Message;

	class TestStateHandler extends AbstractMessageHandler {
		/** @inheritdoc */
		public function canHandle(IMessage $message): bool {
			return $message->getType() === 'state';
		}

		/** @inheritdoc */
		public function request(IMessage $message): IMessage {
		}

		/** @inheritdoc */
		public function response(IMessage $message): IMessage {
		}
	}
