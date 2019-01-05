<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\SimpleObject;
	use stdClass;
	use function iterator_to_array;

	class Packet extends SimpleObject implements IPacket {
		/** @var array */
		protected $packet = [];

		public function __construct() {
			$this->packet = [
				'messages' => [],
			];
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
				'messages' => iterator_to_array((function (array $messages) {
					/** @var $messages IMessage[] */
					foreach ($messages as $message) {
						yield $message->export();
					}
				})($this->messages())),
			];
		}
	}
