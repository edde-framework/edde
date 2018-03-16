<?php
	declare(strict_types=1);
	namespace Edde\Xml;

	use Edde\Exception\Xml\XmlParserException;
	use Edde\Node\INode;
	use Edde\Node\Node;

	/**
	 * Static XML tree handler; reads whole XML input into a memory.
	 */
	class XmlNodeHandler extends AbstractXmlHandler {
		/** @var INode */
		protected $node;
		/** @var INode */
		protected $current;

		/**
		 * @param INode $node
		 */
		public function __construct(INode $node = null) {
			$this->current = $this->node = $node;
		}

		/** @inheritdoc */
		public function onTextEvent(string $text): void {
			$this->current ? $this->current->setValue($text) : null;
		}

		/** @inheritdoc */
		public function onDocTypeEvent(string $docType): void {
			$this->node->add(new Node($docType));
		}

		/** @inheritdoc */
		public function onOpenTagEvent(string $tag, array $attributes): void {
			if ($this->node === null) {
				$this->current = $this->node = new Node($tag, $attributes, null);
				return;
			}
			$this->current->add($node = new Node($tag, $attributes, null));
			$this->current = $node;
		}

		/** @inheritdoc */
		public function onCloseTagEvent(string $name): void {
			$this->current = $this->current->getParent();
		}

		/** @inheritdoc */
		public function onShortTagEvent(string $tag, array $attributes): void {
			if ($this->node === null) {
				$this->node = new Node($tag, $attributes, null);
				return;
			}
			$this->current->add(new Node($tag, $attributes, null));
		}

		/** @inheritdoc */
		public function onHeaderEvent(string $header): void {
		}

		/**
		 * @return INode
		 *
		 * @throws XmlParserException
		 */
		public function getNode(): INode {
			if ($this->node === null) {
				throw new XmlParserException('Nothing has been parsed. One cute kitten will be killed because of you!');
			}
			return $this->node;
		}
	}
