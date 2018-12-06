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
		 * @param array|null  $attrs
		 * @param string|null $uuid
		 *
		 * @return IMessage
		 */
		public function createMessage(string $type, string $namespace, array $attrs = null, string $uuid = null): IMessage;
	}
