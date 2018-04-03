<?php
	declare(strict_types=1);
	namespace Edde\Service\Bus;

	use Edde\Bus\IMessageBus;

	trait MessageBus {
		/** @var IMessageBus */
		protected $messageBus;

		/**
		 * @param IMessageBus $messageBus
		 */
		public function injectMessageBus(IMessageBus $messageBus) {
			$this->messageBus = $messageBus;
		}
	}
