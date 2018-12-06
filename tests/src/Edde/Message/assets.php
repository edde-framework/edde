<?php
	namespace Edde\Message;

	use Edde\Edde;

	class StateMessageHandler extends AbstractMessageHandler {
		/** @inheritdoc */
		public function message(IMessage $message, IPacket $packet): IMessageHandler {
			$packet->message($this->reply($message, [
				'foo' => 'bar',
			]));
			return $this;
		}

		/** @inheritdoc */
		public function pull(IMessage $message, IPacket $packet): IMessageHandler {
			return $this;
		}
	}

	class DummyMessageHandler extends Edde {
	}
