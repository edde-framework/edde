<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Edde;
	use Edde\Service\Storage\Storage;

	class MessageQueue extends Edde implements IMessageQueue {
		use Storage;

		/** @inheritdoc */
		public function enqueue(): IMessageQueue {
			return $this;
		}

		/** @inheritdoc */
		public function execute(): IMessageQueue {
			return $this;
		}
	}
