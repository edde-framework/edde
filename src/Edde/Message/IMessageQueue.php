<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use DateTime;
	use Edde\Configurable\IConfigurable;

	interface IMessageQueue extends IConfigurable {
		/**
		 * send a message into queue
		 *
		 * @param IMessage $message a message going to queue
		 * @param DateTime $time    if time is not set, message is executed "immediately" asynchronously
		 *
		 * @return IMessageQueue
		 */
		public function send(IMessage $message, DateTime $time = null): IMessageQueue;

		/**
		 * enqueue messages (mark messages for execution)
		 *
		 * @return string message queue batch uuid
		 */
		public function enqueue(): string;

		/**
		 * actually executes message queue; messages must be prepared from
		 * enqueue step or the queue should not do nothing
		 *
		 * @return IMessageQueue
		 */
		public function execute(string $batch): IMessageQueue;
	}
