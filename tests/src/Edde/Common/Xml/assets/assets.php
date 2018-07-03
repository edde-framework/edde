<?php
	use Edde\Common\Xml\AbstractXmlHandler;

	class TestXmlHandler extends AbstractXmlHandler {
		/**
		 * @var array
		 */
		protected $tagList = [];

		/**
		 * @return array
		 */
		public function getTagList() {
			return $this->tagList;
		}

		public function onTextEvent(string $text) {
		}

		public function onDocTypeEvent(string $docType) {
		}

		public function onOpenTagEvent(string $tag, array $attributeList) {
			$this->tagList[] = [
				$tag,
				$attributeList,
			];
		}

		public function onCloseTagEvent(string $name) {
		}

		public function onShortTagEvent(string $tag, array $attributeList) {
			$this->tagList[] = [
				$tag,
				$attributeList,
			];
		}

		public function onHeaderEvent(string $header) {
		}
	}
