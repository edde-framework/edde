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
		public function message(IMessage $message): IPacket;

		/**
		 * return an array of request messages
		 *
		 * @return IMessage[]
		 */
		public function messages(): array;

		/**
		 * export packet as an standard object
		 *
		 * @return stdClass
		 */
		public function export(): stdClass;
	}
