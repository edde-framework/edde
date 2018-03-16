<?php
	declare(strict_types=1);
	use Edde\Common\Xml\AbstractXmlHandler;

	class TestXmlHandler extends AbstractXmlHandler {
		/** @var array */
		protected $tags = [];

		/**
		 * @return array
		 */
		public function getTags() {
			return $this->tags;
		}

		public function onTextEvent(string $text): void {
		}

		public function onDocTypeEvent(string $docType): void {
		}

		public function onOpenTagEvent(string $tag, array $attributes): void {
			$this->tags[] = [
				$tag,
				$attributes,
			];
		}

		public function onCloseTagEvent(string $name): void {
		}

		public function onShortTagEvent(string $tag, array $attributes): void {
			$this->tags[] = [
				$tag,
				$attributes,
			];
		}

		public function onHeaderEvent(string $header): void {
		}
	}
