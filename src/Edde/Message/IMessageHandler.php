<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Configurable\IConfigurable;

	interface IMessageHandler extends IConfigurable {
		/**
		 * process input message in request mode (thus input message is in "request" part)
		 *
		 * @param IMessage $message
		 *
		 * @return IMessage
		 */
		public function request(IMessage $message): IMessage;

		/**
		 * process input message in response mode
		 *
		 * @param IMessage $message
		 *
		 * @return IMessage
		 */
		public function response(IMessage $message): IMessage;
	}
