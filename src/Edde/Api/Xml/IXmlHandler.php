<?php
	declare(strict_types=1);
	namespace Edde\Api\Xml;

	use Edde\Config\IConfigurable;

	/**
	 * Implementation of xml handler.
	 */
	interface IXmlHandler extends IConfigurable {
		/**
		 * @param string $text
		 */
		public function onTextEvent(string $text): void;

		/**
		 * @param string $docType
		 */
		public function onDocTypeEvent(string $docType): void;

		/**
		 * @param string $tag
		 * @param array  $attributeList
		 */
		public function onOpenTagEvent(string $tag, array $attributeList): void;

		/**
		 * @param string $name
		 */
		public function onCloseTagEvent(string $name): void;

		/**
		 * @param string $tag
		 * @param array  $attributeList
		 */
		public function onShortTagEvent(string $tag, array $attributeList): void;

		/**
		 * @param string $header
		 */
		public function onHeaderEvent(string $header): void;
	}
