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
		 *
		 * @throws MessageException
		 */
		public function packet(IPacket $packet): IPacket;

		/**
		 * resolve message handler for the given message
		 *
		 * @param IMessage $message
		 *
		 * @return IMessageHandler
		 *
		 * @throws MessageException
		 */
		public function resolve(IMessage $message): IMessageHandler;

		/**
		 * create a packet
		 *
		 * @return IPacket
		 */
		public function createPacket(): IPacket;
	}
