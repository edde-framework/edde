<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Edde;
	use stdClass;
	use function iterator_to_array;

	class Packet extends Edde implements IPacket {
		/** @var string */
		protected $version;
		/** @var string */
		protected $uuid;
		/** @var IMessage[] */
		protected $push;
		/** @var IMessage[] */
		protected $pull;

		/**
		 * @param string $version
		 * @param string $uuid
		 */
		public function __construct(string $version, string $uuid) {
			$this->version = $version;
			$this->uuid = $uuid;
			$this->push = [];
			$this->pull = [];
		}

		/** @inheritdoc */
		public function getVersion(): string {
			return $this->version;
		}

		/** @inheritdoc */
		public function getUuid(): string {
			return $this->uuid;
		}

		/** @inheritdoc */
		public function push(IMessage $message): IPacket {
			$this->push[] = $message;
			return $this;
		}

		/** @inheritdoc */
		public function pushes(): array {
			return $this->push;
		}

		/** @inheritdoc */
		public function pull(IMessage $message): IPacket {
			$this->pull[] = $message;
			return $this;
		}

		/** @inheritdoc */
		public function pulls(): array {
			return $this->pull;
		}

		/** @inheritdoc */
		public function export(): stdClass {
			return (object)[
				'version' => $this->getVersion(),
				'push'    => iterator_to_array((function (array $messages) {
					/** @var $messages IMessage[] */
					foreach ($messages as $message) {
						yield $message->export();
					}
				})($this->pushes())),
				'pull'    => iterator_to_array((function (array $messages) {
					/** @var $messages IMessage[] */
					foreach ($messages as $message) {
						yield $message->export();
					}
				})($this->pulls())),
			];
		}
	}
