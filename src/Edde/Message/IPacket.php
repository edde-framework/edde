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
		 * add a message which should be executed "on the other side"
		 *
		 * @param IMessage $message
		 *
		 * @return IPacket
		 */
		public function push(IMessage $message): IPacket;

		/**
		 * return an array of request messages
		 *
		 * @return IMessage[]
		 */
		public function pushes(): array;

		/**
		 * add a message which should be executed as a "response" on "the other side"
		 *
		 * @param IMessage $message
		 *
		 * @return IPacket
		 */
		public function pull(IMessage $message): IPacket;

		/**
		 * return an array of pulled messages
		 *
		 * @return IMessage[]
		 */
		public function pulls(): array;

		/**
		 * export packet as an standard object
		 *
		 * @return stdClass
		 */
		public function export(): stdClass;
	}
