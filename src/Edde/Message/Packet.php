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
		protected $request;
		/** @var IMessage[] */
		protected $response;

		/**
		 * @param string $version
		 * @param string $uuid
		 */
		public function __construct(string $version, string $uuid) {
			$this->version = $version;
			$this->uuid = $uuid;
			$this->request = [];
			$this->response = [];
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
		public function request(IMessage $message): IPacket {
			$this->request[] = $message;
			return $this;
		}

		/** @inheritdoc */
		public function requests(): array {
			return $this->request;
		}

		/** @inheritdoc */
		public function response(IMessage $message): IPacket {
			$this->response[] = $message;
			return $this;
		}

		/** @inheritdoc */
		public function responses(): array {
			return $this->response;
		}

		/** @inheritdoc */
		public function export(): stdClass {
			return (object)[
				'version'  => $this->getVersion(),
				'request'  => iterator_to_array((function (array $messages) {
					/** @var $messages IMessage[] */
					foreach ($messages as $message) {
						yield $message->export();
					}
				})($this->requests())),
				'response' => iterator_to_array((function (array $messages) {
					/** @var $messages IMessage[] */
					foreach ($messages as $message) {
						yield $message->export();
					}
				})($this->responses())),
			];
		}
	}
