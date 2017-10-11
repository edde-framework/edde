<?php
	namespace Edde\Common\Content;

		class ScalarContent extends Content {
			public function __construct($content) {
				parent::__construct($content, 'scalar');
			}
		}
