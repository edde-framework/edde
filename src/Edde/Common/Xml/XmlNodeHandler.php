<?php
	declare(strict_types=1);
	namespace Edde\Common\Xml;

	use Edde\Common\Node\Node;
	use Edde\Exception\Xml\XmlParserException;
	use Edde\Node\INode;

	/**
	 * Static XML tree handler; reads whole XML input into a memory.
	 */
	class XmlNodeHandler extends AbstractXmlHandler {
		/**
		 * @var INode
		 */
		protected $node;
		/**
		 * @var \Edde\Node\INode
		 */
		protected $current;

		/**
		 * @param \Edde\Node\INode $node
		 */
		public function __construct(INode $node = null) {
			$this->current = $this->node = $node;
		}

		public function onTextEvent(string $text): void {
			$this->current ? $this->current->setValue($text) : null;
		}

		public function onDocTypeEvent(string $docType): void {
			$this->node->add(new Node($docType));
		}

		public function onOpenTagEvent(string $tag, array $attributes): void {
			if ($this->node === null) {
				$this->current = $this->node = new Node($tag, $attributes, null);
				return;
			}
			$this->current->add($node = new Node($tag, $attributes, null));
			$this->current = $node;
		}

		public function onCloseTagEvent(string $name): void {
			$this->current = $this->current->getParent();
		}

		public function onShortTagEvent(string $tag, array $attributes): void {
			if ($this->node === null) {
				$this->node = new Node($tag, $attributes, null);
				return;
			}
			$this->current->add(new Node($tag, $attributes, null));
		}

		public function onHeaderEvent(string $header): void {
		}

		/**
		 * @return \Edde\Node\INode
		 * @throws XmlParserException
		 */
		public function getNode(): INode {
			if ($this->node === null) {
				throw new XmlParserException('Nothing has been parsed. One cute kitten will be killed because of you!');
			}
			return $this->node;
		}
	}
