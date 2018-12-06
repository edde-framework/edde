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
		 * @param string $namespace
		 * @param array  $attrs
		 * @param string $uuid
		 */
		public function __construct(string $type, string $namespace, string $uuid, array $attrs = null) {
			$this->message = [
				'type'      => $type,
				'namespace' => $namespace,
				'uuid'      => $uuid,
				'attrs'     => $attrs,
			];
		}

		/** @inheritdoc */
		public function getType(): string {
			return (string)$this->message['type'];
		}

		/** @inheritdoc */
		public function getNamespace(): string {
			return (string)$this->message['namespace'];
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
