<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Edde;
	use stdClass;
	use function iterator_to_array;

	class Packet extends Edde implements IPacket {
		/** @var array */
		protected $packet = [];

		/**
		 * @param string $version
		 * @param string $uuid
		 */
		public function __construct(string $version, string $uuid) {
			$this->packet = [
				'version'  => $version,
				'uuid'     => $uuid,
				'messages' => [],
			];
		}

		/** @inheritdoc */
		public function getVersion(): string {
			return (string)$this->packet['version'];
		}

		/** @inheritdoc */
		public function getUuid(): string {
			return (string)$this->packet['uuid'];
		}

		/** @inheritdoc */
		public function message(IMessage $message): IPacket {
			$this->packet['messages'][] = $message;
			return $this;
		}

		/** @inheritdoc */
		public function messages(): array {
			return $this->packet['messages'];
		}

		/** @inheritdoc */
		public function export(): stdClass {
			return (object)[
				'version'  => $this->getVersion(),
				'messages' => iterator_to_array((function (array $messages) {
					/** @var $messages IMessage[] */
					foreach ($messages as $message) {
						yield $message->export();
					}
				})($this->messages())),
			];
		}
	}
