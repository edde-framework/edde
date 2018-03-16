<?php
	declare(strict_types=1);
	namespace Edde\Content;

	class GeneratorContent extends Content {
		public function __construct(callable $content, string $type) {
			parent::__construct($content, $type);
		}

		/** @inheritdoc */
		public function getIterator() {
			$content = $this->content;
			return $content();
		}
	}
