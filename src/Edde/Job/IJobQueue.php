<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use DateTime;
	use Edde\Configurable\IConfigurable;
	use Edde\Message\IMessage;

	interface IJobQueue extends IConfigurable {
		/**
		 * send a message into queue
		 *
		 * @param IMessage $message a message going to queue
		 * @param DateTime $time    if time is not set, message is executed "immediately" asynchronously
		 *
		 * @return IJobQueue
		 */
		public function enqueue(IMessage $message, DateTime $time = null): IJobQueue;

		/**
		 * execute the given message uuid
		 *
		 * @return IJobQueue
		 */
		public function execute(string $message): IJobQueue;
	}
