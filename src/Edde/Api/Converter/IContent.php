<?php
	declare(strict_types=1);

	namespace Edde\Api\Converter;

	interface IContent {
		/**
		 * return content of this... content
		 *
		 * @return mixed
		 */
		public function getContent();

		/**
		 * return (mime) type of this content
		 *
		 * @return string
		 */
		public function getMime(): string;
	}
