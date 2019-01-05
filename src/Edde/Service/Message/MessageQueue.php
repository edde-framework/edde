<?php
	declare(strict_types=1);
	namespace Edde\Service\Message;

	use Edde\Job\IJobQueue;

	trait MessageQueue {
		/** @var IJobQueue */
		protected $messageQueue;

		/**
		 * @param \Edde\Job\IJobQueue $messageQueue
		 */
		public function injectMessageQueue(IJobQueue $messageQueue): void {
			$this->messageQueue = $messageQueue;
		}
	}
