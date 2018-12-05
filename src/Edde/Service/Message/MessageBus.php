<?php
	declare(strict_types=1);
	namespace Edde\Service\Message;

	use Edde\Message\IMessageBus;

	trait MessageBus {
		/** @var IMessageBus */
		protected $messageBus;

		/**
		 * @param IMessageBus $messageBus
		 */
		public function injectMessageBus(IMessageBus $messageBus): void {
			$this->messageBus = $messageBus;
		}
	}
