<?php
	namespace Edde\Message;

	use Edde\Edde;

	class CommonMessageService extends AbstractMessageService {
		/** @inheritdoc */
		public function onStateMessage(IMessage $message, IPacket $packet): IMessageService {
			$packet->message($this->reply($message, [
				'foo' => 'bar',
			]));
			return $this;
		}
	}

	class DummyMessageHandler extends Edde {
	}
