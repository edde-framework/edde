<?php
	declare(strict_types=1);

	namespace Edde\Common\Html;

	use Edde\Api\Html\IHtmlGenerator;
	use Edde\Api\Node\IAttributeList;
	use Edde\Api\Node\INode;
	use Edde\Common\Node\AbstractTreeTraversal;
	use Edde\Common\Node\NodeIterator;

	/**
	 * Common html5 generator.
	 */
	class Html5Generator extends AbstractTreeTraversal implements IHtmlGenerator {
		/**
		 * @inheritdoc
		 */
		public function getTagList(): array {
			return [
				'html',
				'head',
				'meta',
				'title',
				'link',
				'body',
				'div',
				'span',
				'img',
				'table',
				'thead',
				'tbody',
				'tfoot',
				'td',
				'tr',
				'th',
				'h1',
				'h2',
				'h3',
				'h4',
				'h5',
				'h6',
				'section',
				'p',
				'label',
				'input',
				'select',
				'option',
				'button',
				'script',
				'a',
			];
		}

		/**
		 * @inheritdoc
		 */
		public function render(INode $root): IHtmlGenerator {
			$this->traverse($root, NodeIterator::recursive($root));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function generate(INode $root): string {
			ob_start();
			$this->render($root);
			return ob_get_clean();
		}

		/**
		 * @inheritdoc
		 */
		public function open(INode $node, int $level = null): string {
			$content = '';
			switch ($node->getName()) {
				case 'html':
					$content .= "<!DOCTYPE html>\n";
					break;
			}
			$content .= str_repeat("\t", $level ?? $node->getLevel());
			$content .= '<' . $node->getName();
			foreach ($node->getAttributeList() as $name => $value) {
				if ($value instanceof IAttributeList) {
					continue;
				}
				$content .= ' ' . $name . '="' . ($value ? ($value instanceof \Closure ? $value() : htmlspecialchars((string)$value, ENT_QUOTES)) : '') . '"';
			}
			$content .= '>';
			if ($node->isLeaf() === false) {
				$content .= "\n";
			}
			return $content;
		}

		/**
		 * @inheritdoc
		 */
		public function content(INode $node): string {
			return trim(is_callable($value = $node->getValue()) ? $value() : (string)$value);
		}

		/**
		 * @inheritdoc
		 */
		public function close(INode $node, int $level = null): string {
			switch ($node->getName()) {
				case 'meta':
				case 'link':
				case 'input':
					return '';
					break;
			}
			$content = $node->isLeaf() === false ? str_repeat("\t", $level?? $node->getLevel()) : '';
			$content .= '</' . $node->getName() . '>';
			return $content;
		}

		/**
		 * @inheritdoc
		 */
		public function enter(INode $node, \Iterator $iterator, ...$parameters) {
			echo $this->open($node);
		}

		/**
		 * @inheritdoc
		 */
		public function node(INode $node, \Iterator $iterator, ...$parameters) {
			if (($content = $this->content($node)) !== '') {
				echo $content;
				return;
			}
			parent::node($node, $iterator, ...$parameters);
		}

		/**
		 * @inheritdoc
		 */
		public function leave(INode $node, \Iterator $iterator, ...$parameters) {
			echo $this->close($node);
		}
	}
