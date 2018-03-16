<?php
	declare(strict_types=1);
	namespace Edde\Element;

	class Message extends Element implements \Edde\Element\IMessage {
		public function __construct(string $uuid) {
			parent::__construct('message', $uuid, ['version' => '2.0']);
		}
	}
