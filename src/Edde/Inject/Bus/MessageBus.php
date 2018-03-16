<?php
	declare(strict_types=1);
	namespace Edde\Inject\Bus;

	use Edde\Api\Bus\IMessageBus;

	trait MessageBus {
		/** @var IMessageBus */
		protected $messageBus;

		/**
		 * @param IMessageBus $messageBus
		 */
		public function lazyMessageBus(IMessageBus $messageBus) {
			$this->messageBus = $messageBus;
		}
	}
