<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Configurable\IConfigurable;

	interface IMessageQueue extends IConfigurable {
		/**
		 * enqueue messages (mark messages for execution)
		 *
		 * @return IMessageQueue
		 */
		public function enqueue(): IMessageQueue;

		/**
		 * actually executes message queue; messages must be prepared from
		 * enqueue step or the queue should not do nothing
		 *
		 * @return IMessageQueue
		 */
		public function execute(string $batch): IMessageQueue;
	}
