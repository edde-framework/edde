<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Edde;
	use Edde\Service\Security\RandomService;

	abstract class AbstractMessageHandler extends Edde implements IMessageHandler {
		use RandomService;

		/** @inheritdoc */
		public function createMessage(string $type, string $namespace, string $uuid = null, array $attrs = null): IMessage {
			return new Message($type, $namespace, $uuid ?: $this->randomService->uuid(), $attrs);
		}

		protected function reply(IMessage $message, array $attrs = null): IMessage {
			return $this->createMessage($message->getType(), $message->getNamespace(), null, $attrs);
		}
	}
