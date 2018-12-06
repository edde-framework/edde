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
		 * get namespace of this message (could be used for service handler translation)
		 *
		 * @return string
		 */
		public function getNamespace(): string;

		/**
		 * every message should have unique id (uuid v4)
		 *
		 * @return string
		 */
		public function getUuid(): string;

		/**
		 * @return array
		 */
		public function getAttrs(): ?array;

		/**
		 * export message as an standard object (to be serialized)
		 *
		 * @return stdClass
		 */
		public function export(): stdClass;
	}
