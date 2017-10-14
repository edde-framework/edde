<?php
	namespace Edde\Common\Content;

		class HtmlContent extends Content {
			public function __construct(string $content, string $type = 'text/html') {
				parent::__construct($content, $type);
			}
		}
