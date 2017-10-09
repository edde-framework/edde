<?php
	namespace Edde\Common\Element;

		class Message extends Element {
			public function __construct(string $request) {
				parent::__construct('message');
				$this->setAttribute('request', $request);
			}

			public function getRequest(): string {
				return $this->getAttribute('request');
			}
		}
