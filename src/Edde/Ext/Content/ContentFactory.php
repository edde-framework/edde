<?php
	declare(strict_types=1);
	namespace Edde\Ext\Content;

		use Edde\Api\Content\IContent;
		use Edde\Api\Element\IElement;
		use Edde\Common\Content\ElementContent;
		use Edde\Common\Content\JsonContent;
		use Edde\Common\Content\TextContent;

		/**
		 * Simple trait used as a factory for content in "views" or "controllers" or
		 * whatever to simplify way how to create different types of a content supported
		 * by the framework.
		 */
		trait ContentFactory {
			/**
			 * create an application/json content type; that means raw data are on input,
			 * encoded content should be on output
			 *
			 * @param mixed $content
			 *
			 * @return IContent
			 */
			public function contentJson($content) : IContent {
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
			public function contentText(string $content) : IContent {
				return new TextContent($content);
			}

			/**
			 * create an element content, usually related to the protocol stuff
			 *
			 * @param IElement $element
			 *
			 * @return IContent
			 */
			public function contentElement(IElement $element) : IContent {
				return new ElementContent($element);
			}
		}
