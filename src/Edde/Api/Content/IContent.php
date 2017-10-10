<?php
	declare(strict_types=1);
	namespace Edde\Api\Content;

	/**
	 * Simple interface describing some "content" with it's mime type.
	 */
		interface IContent {
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
