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

		/**
		 * @param string $type
		 * @param string $resource
		 * @param string $uuid
		 */
		public function __construct(string $type, string $resource, string $uuid) {
			$this->type = $type;
			$this->resource = $resource;
			$this->uuid = $uuid;
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
		public function export(): stdClass {
			return (object)[
				'type' => $this->getType(),
			];
		}
	}
