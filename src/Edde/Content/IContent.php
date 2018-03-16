<?php
	declare(strict_types=1);
	namespace Edde\Content;

	use IteratorAggregate;

	/**
	 * Simple interface describing some "content" with it's mime type.
	 *
	 * Content supports streaming (iterator aggregate), even if it is a simple
	 * content, it should stream itself by iterator.
	 */
	interface IContent extends IteratorAggregate {
		/**
		 * return the raw content
		 *
		 * @return mixed
		 */
		public function getContent();

		/**
		 * get type of the content; could be mime type, but it's
		 * not restricted just to that
		 *
		 * @return string
		 */
		public function getType(): string;
	}
