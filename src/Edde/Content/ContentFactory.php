<?php
	declare(strict_types=1);
	namespace Edde\Content;

	/**
	 * Simple trait used as a factory for content in "views" or "controllers" or
	 * whatever to simplify way how to create different types of a content supported
	 * by the framework.
	 */
	trait ContentFactory {
		/**
		 * send scalar content; useful for conversion to json, serialization or whatever other output
		 *
		 * @param mixed $content
		 *
		 * @return IContent
		 */
		public function contentScalar($content): IContent {
			return new ScalarContent($content);
		}

		/**
		 * create an application/json content type; that means raw data are on input,
		 * encoded content should be on output
		 *
		 * @param string $content
		 *
		 * @return IContent
		 */
		public function contentJson(string $content): IContent {
			return new JsonContent($content);
		}

		/**
		 * create a new text content; input is intentionally restricted to string as
		 * it could eventually fail in early stage of the request than somewhere later, for
		 * example if content is a template converted later and it throws some exception,
		 * it cannot be simply handled
		 *
		 * @param string $content
		 *
		 * @return IContent
		 */
		public function contentText(string $content): IContent {
			return new TextContent($content);
		}
	}
