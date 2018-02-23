<?php
	declare(strict_types=1);
	namespace Edde\Common\Bus;

	use Edde\Api\Bus\IMessage;

	class Message extends Element implements IMessage {
		public function __construct(string $uuid) {
			parent::__construct('message', $uuid, ['version' => '2.0']);
		}
	}
