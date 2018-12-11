<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Edde;
	use Edde\Service\Security\RandomService;

	abstract class AbstractMessageService extends Edde implements IMessageService {
		use RandomService;

		/** @inheritdoc */
		public function createMessage(string $type, string $namespace, array $attrs = null, string $uuid = null): IMessage {
			return new Message($type, $namespace, $uuid ?: $this->randomService->uuid(), $attrs);
		}

		protected function reply(IMessage $message, array $attrs = null): IMessage {
			return $this->createMessage($message->getType(), $message->getNamespace(), $attrs, null);
		}
	}
