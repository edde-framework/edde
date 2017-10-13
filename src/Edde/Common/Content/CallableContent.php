<?php
	namespace Edde\Common\Content;

		class CallableContent extends Content {
			public function __construct(callable $content) {
				parent::__construct($content, 'callable');
			}
		}
