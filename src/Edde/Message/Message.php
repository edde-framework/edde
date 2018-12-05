<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Edde;
	use stdClass;

	class Message extends Edde implements IMessage {
		/** @var string */
		protected $type;
		/** @var string */
		protected $resource;
		/** @var string */
		protected $uuid;
		/** @var array */
		protected $attrs;

		/**
		 * @param string $type
		 * @param string $resource
		 * @param array  $attrs
		 * @param string $uuid
		 */
		public function __construct(string $type, string $resource, string $uuid, array $attrs = null) {
			$this->type = $type;
			$this->resource = $resource;
			$this->uuid = $uuid;
			$this->attrs = $attrs ?? [];
		}

		/** @inheritdoc */
		public function getType(): string {
			return $this->type;
		}

		/** @inheritdoc */
		public function getResource(): string {
			return $this->resource;
		}

		/** @inheritdoc */
		public function getUuid(): string {
			return $this->uuid;
		}

		/** @inheritdoc */
		public function getAttrs(): array {
			return $this->attrs;
		}

		/** @inheritdoc */
		public function export(): stdClass {
			return (object)[
				'type'     => $this->getType(),
				'resource' => $this->getResource(),
				'uuid'     => $this->getUuid(),
				'attrs'    => (object)$this->getAttrs(),
			];
		}
	}
