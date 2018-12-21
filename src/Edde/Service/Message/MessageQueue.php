<?php
	declare(strict_types=1);
	namespace Edde\Service\Message;

	use Edde\Message\IMessageQueue;

	trait MessageQueue {
		/** @var IMessageQueue */
		protected $messageQueue;

		/**
		 * @param IMessageQueue $messageQueue
		 */
		public function injectMessageQueue(IMessageQueue $messageQueue): void {
			$this->messageQueue = $messageQueue;
		}
	}
