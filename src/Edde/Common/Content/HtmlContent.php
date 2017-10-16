<?php
	namespace Edde\Common\Content;

		class HtmlContent extends Content {
			public function __construct($content, string $type = 'text/html') {
				parent::__construct($content, $type);
			}

			public function getIterator() {
				if (is_string($content = $this->content)) {
					$content = [$content];
				}
				yield from $content;
			}
		}
