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
		 * @param string      $service
		 * @param string      $type
		 * @param array|null  $attrs
		 * @param string|null $uuid
		 *
		 * @return IMessage
		 */
		public function createMessage(string $service, string $type, array $attrs = null, string $uuid = null): IMessage;
	}
