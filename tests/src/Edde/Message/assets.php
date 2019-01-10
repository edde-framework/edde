<?php
	namespace Edde\Pub\Message;

	use Edde\Edde;
	use Edde\Message\AbstractMessageService;
	use Edde\Message\IMessage;
	use Edde\Message\IMessageService;
	use Edde\Message\IPacket;
	use Edde\Message\Message;

	class CommonMessageService extends AbstractMessageService {
		/** @inheritdoc */
		public function onStateMessage(IMessage $message, IPacket $packet): IMessageService {
			$packet->message($this->reply($message, [
				'foo' => 'bar',
			]));
			return $this;
		}

		public function onAsyncMessage(IMessage $message, IPacket $packet) {
			$packet->message(new Message('mwah', 'it works', $message->getAttrs()));
		}
	}

	class DummyMessageHandler extends Edde {
	}
