<?php
	declare(strict_types=1);
	namespace Edde\Common\Bus;

	use Edde\Api\Bus\IElement;
	use Edde\Api\Bus\IMessageService;
	use Edde\Inject\Bus\MessageBus;
	use Edde\Inject\Crypt\RandomService;

	class MessageService extends AbstractHandler implements IMessageService {
		use RandomService;
		use MessageBus;

		/** @inheritdoc */
		public function canHandle(IElement $element): bool {
			return $element->getType() === 'message';
		}

		/** @inheritdoc */
		public function execute(IElement $element): ?IElement {
			$message = new Message($this->randomService->uuid());
			foreach ($element->getSends() as $send) {
				$message->response($send->getUuid(), $this->messageBus->send($send));
			}
			foreach ($element->getExecutes() as $execute) {
				$message->response($execute->getUuid(), $this->messageBus->execute($execute));
			}
			return $message;
		}
	}
