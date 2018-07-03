<?php
	declare(strict_types=1);

	namespace Edde\Api\Xml;

	/**
	 * Implementation of xml handler.
	 */
	interface IXmlHandler {
		/**
		 * @param string $text
		 */
		public function onTextEvent(string $text);

		/**
		 * @param string $docType
		 */
		public function onDocTypeEvent(string $docType);

		/**
		 * @param string $tag
		 * @param array  $attributeList
		 */
		public function onOpenTagEvent(string $tag, array $attributeList);

		/**
		 * @param string $name
		 */
		public function onCloseTagEvent(string $name);

		/**
		 * @param string $tag
		 * @param array  $attributeList
		 */
		public function onShortTagEvent(string $tag, array $attributeList);

		/**
		 * @param string $header
		 */
		public function onHeaderEvent(string $header);
	}
