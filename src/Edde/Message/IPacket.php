<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use stdClass;

	interface IPacket {
		/**
		 * get packet version (alias message bus version)
		 *
		 * @return string
		 */
		public function getVersion(): string;

		/**
		 * uuid of packet should be... universally unique
		 *
		 * @return string
		 */
		public function getUuid(): string;

		/**
		 * add a message to requests
		 *
		 * @param IMessage $message
		 *
		 * @return IPacket
		 */
		public function request(IMessage $message): IPacket;

		/**
		 * return an array of request messages
		 *
		 * @return IMessage[]
		 */
		public function requests(): array;

		/**
		 * add a message to responses
		 *
		 * @param IMessage $message
		 *
		 * @return IPacket
		 */
		public function response(IMessage $message): IPacket;

		/**
		 * return an array of response messages
		 *
		 * @return IMessage[]
		 */
		public function responses(): array;

		/**
		 * export packet as an standard object
		 *
		 * @return stdClass
		 */
		public function export(): stdClass;
	}
