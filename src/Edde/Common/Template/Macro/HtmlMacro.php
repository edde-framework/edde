<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Html\LazyHtmlGeneratorTrait;
	use Edde\Api\Node\IAttributeList;
	use Edde\Api\Node\INode;
	use Edde\Common\Template\AbstractMacro;

	class HtmlMacro extends AbstractMacro {
		use LazyHtmlGeneratorTrait;

		/**
		 * @inheritdoc
		 */
		public function getNameList(): array {
			return $this->htmlGenerator->getTagList();
		}

		/**
		 * @inheritdoc
		 */
		public function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			$attributeList = $node->getAttributeList();
			$attributes = [];
			foreach ($attributeList as $k => $v) {
				$attributes[$k] = $v instanceof IAttributeList ? $v : function () use ($v) {
					return ($delimite = $this->delimite($v, true)) === $v ? preg_replace_callback('~{\?(.*)?}~', function ($item) {
						return '<?=' . $this->delimite($item[1]) . '?>';
					}, htmlspecialchars((string)$v), ENT_QUOTES) : '<?=' . $delimite . '?>';
				};
			}
			$attributeList->put($attributes);
			if ($node->hasAttribute('::value')) {
				$node->setValue($node->getAttribute('::value'));
				$node->getAttributeList()->remove('::value');
			}
			echo $this->htmlGenerator->open($node);
		}

		/**
		 * @inheritdoc
		 */
		public function onNode(INode $node, \Iterator $iterator, ...$parameters) {
			if (($content = $this->htmlGenerator->content($node)) !== '') {
				echo $content;
				return;
			}
			parent::onNode($node, $iterator, ...$parameters);
		}

		/**
		 * @inheritdoc
		 */
		public function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			echo $this->htmlGenerator->close($node);
		}
	}
