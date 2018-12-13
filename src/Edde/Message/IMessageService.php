<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Configurable\IConfigurable;

	interface IMessageService extends IConfigurable {
		/**
		 * handle incoming message
		 *
		 * @param IMessage $message message being processed
		 * @param IPacket  $packet  output packet (response)
		 *
		 * @return IMessageService
		 *
		 * @throws MessageException
		 */
		public function message(IMessage $message, IPacket $packet): IMessageService;

		/**
		 * @param string     $type
		 * @param string     $target
		 * @param array|null $attrs
		 *
		 * @return IMessage
		 */
		public function createMessage(string $type, string $target = null, array $attrs = null): IMessage;
	}
