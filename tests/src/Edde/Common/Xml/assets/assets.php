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

		public function onTextEvent(string $text): void {
		}

		public function onDocTypeEvent(string $docType): void {
		}

		public function onOpenTagEvent(string $tag, array $attributeList): void {
			$this->tagList[] = [
				$tag,
				$attributeList,
			];
		}

		public function onCloseTagEvent(string $name): void {
		}

		public function onShortTagEvent(string $tag, array $attributeList): void {
			$this->tagList[] = [
				$tag,
				$attributeList,
			];
		}

		public function onHeaderEvent(string $header): void {
		}
	}
