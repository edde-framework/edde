<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Configurable\IConfigurable;

	interface IMessageHandler extends IConfigurable {
		/**
		 * handle incoming message
		 *
		 * @param IMessage $message message being processed
		 * @param IPacket  $packet  output packet (response)
		 *
		 * @return IMessageHandler
		 *
		 * @throws MessageException
		 */
		public function message(IMessage $message, IPacket $packet): IMessageHandler;

		/**
		 * @param string      $type
		 * @param string      $namespace
		 * @param string|null $uuid
		 * @param array|null  $attrs
		 *
		 * @return IMessage
		 */
		public function createMessage(string $type, string $namespace, string $uuid = null, array $attrs = null): IMessage;
	}
