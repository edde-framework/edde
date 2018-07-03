<?php
	declare(strict_types=1);

	namespace Edde\Common\Request;

	use Edde\Common\Protocol\Element;

	class Message extends Element {
		public function __construct(string $request = null, string $id = null) {
			parent::__construct('message', $id);
			$this->setAttribute('request', $request);
		}
	}
