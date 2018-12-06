<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Configurable\IConfigurable;

	interface IMessageHandler extends IConfigurable {

		/**
		 * messages pushed (from "client" point of view) to the server (alias request)
		 *
		 * @param IMessage $message message being processed
		 * @param IPacket  $packet  output packet (response)
		 *
		 * @return IMessageHandler
		 *
		 * @throws MessageException
		 */
		public function push(IMessage $message, IPacket $packet): IMessageHandler;

		/**
		 * messages pulled (from "client" point of view) to the client (alias response); pulled messages
		 * should be executed by the other side potentially making another roundtrip
		 *
		 * @param IMessage $message
		 * @param IPacket  $packet
		 *
		 * @return IMessageHandler
		 *
		 * @throws MessageException
		 */
		public function pull(IMessage $message, IPacket $packet): IMessageHandler;

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
