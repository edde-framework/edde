<?php
	namespace Edde\Common\Content;

		class IterableContent extends Content {
			public function __construct(callable $content) {
				parent::__construct($content(), \Iterator::class);
			}
		}
