<?php
	namespace Edde\Message;

	use Edde\Edde;

	class StateMessageService extends AbstractMessageService {
		/** @inheritdoc */
		public function message(IMessage $message, IPacket $packet): IMessageService {
			$packet->message($this->reply($message, [
				'foo' => 'bar',
			]));
			return $this;
		}

		/** @inheritdoc */
		public function pull(IMessage $message, IPacket $packet): IMessageService {
			return $this;
		}
	}

//	class CommonMessageService extends AbstractMessageService {
//		public function message(IMessage $message, IPacket $packet): IMessageService {
//		}
//	}

	class DummyMessageHandler extends Edde {
	}
