<?php
	declare(strict_types=1);
	namespace Edde\Common\Content;

	class TextContent extends Content {
		public function __construct($content) {
			parent::__construct($content, 'text/plain');
		}
	}
