<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Edde;
	use Edde\Service\Security\RandomService;

	abstract class AbstractMessageService extends Edde implements IMessageService {
		use RandomService;

		/** @inheritdoc */
		public function createMessage(string $service, string $type, array $attrs = null, string $uuid = null): IMessage {
			return new Message($service, $type, $uuid ?: $this->randomService->uuid(), $attrs);
		}

		protected function reply(IMessage $message, array $attrs = null): IMessage {
			return $this->createMessage($message->getService(), $message->getType(), $attrs, null);
		}
	}
