<?php
	declare(strict_types=1);
	namespace Edde\Api\Bus\Inject;

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
