<?php
	declare(strict_types=1);
	namespace Edde\Content;

	class TextContent extends Content {
		public function __construct($content, string $type = 'text/plain') {
			parent::__construct($content, $type);
		}
	}
