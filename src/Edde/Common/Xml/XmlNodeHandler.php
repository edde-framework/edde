<?php
	declare(strict_types=1);

	namespace Edde\Common\Xml;

	use Edde\Api\Node\INode;
	use Edde\Api\Xml\XmlParserException;
	use Edde\Common\Node\Node;

	/**
	 * Static XML tree handler; reads whole XML input into a memory.
	 */
	class XmlNodeHandler extends AbstractXmlHandler {
		/**
		 * @var INode
		 */
		protected $node;
		/**
		 * @var INode
		 */
		protected $current;

		/**
		 * @param INode $node
		 */
		public function __construct(INode $node = null) {
			$this->current = $this->node = $node;
		}

		public function onTextEvent(string $text) {
			$this->current ? $this->current->setValue($text) : null;
		}

		public function onDocTypeEvent(string $docType) {
			$this->node->addNode(new Node($docType));
		}

		public function onOpenTagEvent(string $tag, array $attributeList) {
			if ($this->node === null) {
				$this->current = $this->node = new Node($tag, null, $attributeList);
				return;
			}
			$this->current->addNode($node = new Node($tag, null, $attributeList));
			$this->current = $node;
		}

		public function onCloseTagEvent(string $name) {
			$this->current = $this->current->getParent();
		}

		public function onShortTagEvent(string $tag, array $attributeList) {
			if ($this->node === null) {
				$this->node = new Node($tag, null, $attributeList);
				return;
			}
			$this->current->addNode(new Node($tag, null, $attributeList));
		}

		public function onHeaderEvent(string $header) {
		}

		public function getNode(): INode {
			if ($this->node === null) {
				throw new XmlParserException('Nothing has been parsed. One cute kitten will be killed because of you!');
			}
			return $this->node;
		}
	}
