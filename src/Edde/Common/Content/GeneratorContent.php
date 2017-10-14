<?php
	namespace Edde\Common\Content;

		class GeneratorContent extends Content {
			public function __construct(callable $content) {
				parent::__construct($content, 'callable');
			}

			public function getIterator() {
				$content = $this->content;
				return $content();
			}
		}
