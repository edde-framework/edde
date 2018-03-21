<?php
	declare(strict_types=1);
	namespace Edde\Bus;

	use Edde\Element\IElement;
	use Edde\Element\Message;
	use Edde\Service\Bus\MessageBus;
	use Edde\Service\Crypt\RandomService;

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
