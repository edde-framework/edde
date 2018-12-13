<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Edde;
	use stdClass;

	class Message extends Edde implements IMessage {
		/** @var array */
		protected $message;

		/**
		 * @param string $type
		 * @param string $target
		 * @param array  $attrs
		 */
		public function __construct(string $type, string $target = null, array $attrs = null) {
			$this->message = [
				'service' => $target,
				'type'    => $type,
				'attrs'   => $attrs,
			];
		}

		/** @inheritdoc */
		public function getType(): string {
			return (string)$this->message['type'];
		}

		/** @inheritdoc */
		public function getTarget(): ?string {
			return ((string)$this->message['service']) ?? null;
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
