<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Configurable\IConfigurable;

	interface IMessageBus extends IConfigurable {
		/**
		 * quite strange version number, but it's high enough to keep line with
		 * edde and make difference from the original The Protocol
		 */
		const VERSION = '5.0';

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

		/**
		 * process individual message
		 *
		 * @param IMessage $message
		 *
		 * @return IMessage
		 */
		public function message(IMessage $message): IMessage;

		/**
		 * process packet (high level method)
		 *
		 * @param IPacket $packet
		 *
		 * @return IPacket
		 */
		public function packet(IPacket $packet): IPacket;

		/**
		 * create a packet
		 *
		 * @return IPacket
		 */
		public function createPacket(): IPacket;

		/**
		 * @param string      $type
		 * @param string      $resource
		 * @param string|null $uuid
		 *
		 * @return IMessage
		 */
		public function createMessage(string $type, string $resource, string $uuid = null): IMessage;
	}
