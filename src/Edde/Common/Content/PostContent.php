<?php
	declare(strict_types=1);
	namespace Edde\Common\Content;

	/**
	 * Post content represents $_POST variable; that means if there would be some converter
	 * or anything working with content sanitization, it should take care about this content
	 * too.
	 */
	class PostContent extends Content {
		public function __construct(array $content) {
			parent::__construct($content, 'post');
		}
	}
