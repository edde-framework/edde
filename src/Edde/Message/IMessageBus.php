<?php
	declare(strict_types=1);
	namespace Edde\Message;

	interface IMessageBus extends IMessageHandler {
		/**
		 * quite strange version number, but it's high enough to keep line with
		 * edde and make difference from the original The Protocol
		 */
		const VERSION = '5.0';

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
