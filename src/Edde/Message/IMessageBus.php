<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use stdClass;

	interface IMessageBus extends IMessageService {
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
		 * @return IMessageService
		 *
		 * @throws MessageException
		 */
		public function resolve(IMessage $message): IMessageService;

		/**
		 * execute a message and return "response" packet
		 *
		 * @param IMessage $message
		 *
		 * @return IPacket
		 */
		public function execute(IMessage $message): IPacket;

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
		public function importPacket(stdClass $import): IPacket;
	}
