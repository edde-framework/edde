<?php
	namespace Edde\Pub\Message;

	use Edde\Edde;
	use Edde\Message\AbstractMessageService;
	use Edde\Message\IMessage;
	use Edde\Message\IPacket;

	class KaboomMessageService extends AbstractMessageService {
	}

	class StateMessageService extends AbstractMessageService {
		/** @inheritdoc */
		public function onStateMessage(IMessage $message, IPacket $packet): void {
			$packet->message($this->reply($message, [
				'foo' => 'bar',
			]));
		}
	}

	class DummyMessageHandler extends Edde {
	}
