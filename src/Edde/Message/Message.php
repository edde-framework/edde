<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Edde;
	use stdClass;

	class Message extends Edde implements IMessage {
		/** @var array */
		protected $message;

		/**
		 * @param string $service
		 * @param string $type
		 * @param array  $attrs
		 * @param string $uuid
		 */
		public function __construct(string $service, string $type, string $uuid, array $attrs = null) {
			$this->message = [
				'service' => $service,
				'type'    => $type,
				'uuid'    => $uuid,
				'attrs'   => $attrs,
			];
		}

		/** @inheritdoc */
		public function getService(): string {
			return (string)$this->message['service'];
		}

		/** @inheritdoc */
		public function getType(): string {
			return (string)$this->message['type'];
		}

		/** @inheritdoc */
		public function getUuid(): string {
			return (string)$this->message['uuid'];
		}

		/** @inheritdoc */
		public function getAttrs(): ?array {
			return $this->message['attrs'];
		}

		/** @inheritdoc */
		public function export(): stdClass {
			return (object)$this->message;
		}
	}
