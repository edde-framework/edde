<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use stdClass;

	/**
	 * Basic element of the whole concept of Message Bus; a message
	 * is an individual piece to be processed on the line.
	 */
	interface IMessage {
		/**
		 * return type of a message
		 *
		 * @return string
		 */
		public function getType(): string;

		/**
		 * get related resource name of this message
		 *
		 * @return string
		 */
		public function getResource(): string;

		/**
		 * every message should have unique id (uuid v4)
		 *
		 * @return string
		 */
		public function getUuid(): string;

		/**
		 * export message as an standard object (to be serialized)
		 *
		 * @return stdClass
		 */
		public function export(): stdClass;
	}
