<?php
	namespace Edde\Message;

	use Edde\Edde;

	class CommonMessageService extends AbstractMessageService {
		public function message(IMessage $message, IPacket $packet): IMessageService {
			return $this;
		}

		/** @inheritdoc */
		public function stateMessage(IMessage $message, IPacket $packet): IMessageService {
			$packet->message($this->reply($message, [
				'foo' => 'bar',
			]));
			return $this;
		}
	}

	class DummyMessageHandler extends Edde {
	}
