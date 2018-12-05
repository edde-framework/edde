<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Edde;

	abstract class AbstractMessageHandler extends Edde implements IMessageHandler {
		/** @inheritdoc */
		public function request(IMessage $message): IMessage {
		}

		/** @inheritdoc */
		public function response(IMessage $message): IMessage {
		}
	}
