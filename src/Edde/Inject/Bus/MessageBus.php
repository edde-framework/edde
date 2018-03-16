<?php
	declare(strict_types=1);
	namespace Edde\Inject\Bus;

	use Edde\Bus\IMessageBus;

	trait MessageBus {
		/** @var IMessageBus */
		protected $messageBus;

		/**
		 * @param \Edde\Bus\IMessageBus $messageBus
		 */
		public function lazyMessageBus(\Edde\Bus\IMessageBus $messageBus) {
			$this->messageBus = $messageBus;
		}
	}
