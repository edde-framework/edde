<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use stdClass;

	interface IMessageBus extends IMessageHandler {
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

		/**
		 * just convert an import into packet; nothing is going to be executed
		 *
		 * @param stdClass $import
		 *
		 * @return IPacket
		 *
		 * @throws MessageException
		 */
		public function import(stdClass $import): IPacket;
	}
