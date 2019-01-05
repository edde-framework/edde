<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use DateTime;
	use Edde\Edde;
	use Edde\Service\Security\RandomService;
	use Edde\Service\Storage\Storage;
	use Edde\Storage\Entity;

	class MessageQueue extends Edde implements IMessageQueue {
		use Storage;
		use RandomService;

		/** @inheritdoc */
		public function send(IMessage $message, DateTime $time = null): IMessageQueue {
			$this->storage->insert(new Entity(MessageQueueSchema::class, [
				'stamp'   => $time,
				'type'    => $message->getType(),
				'target'  => $message->getTarget(),
				'message' => (object)$message->getAttrs(),
			]));
			return $this;
		}

		/** @inheritdoc */
		public function enqueue(): string {
			$uuid = $this->randomService->uuid();
			$this->storage->insert(new Entity(BatchSchema::class, [
				'batch' => $uuid,
			]));
			/**
			 * enqueue messages here!!
			 */
			return $uuid;
		}

		/** @inheritdoc */
		public function execute(string $batch): IMessageQueue {
			return $this;
		}
	}
