<?php
	declare(strict_types=1);

	namespace Edde\Common\Control;

	use Edde\Api\Control\IControl;
	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Api\Protocol\IElement;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Object;

	/**
	 * Root implementation of all controls.
	 */
	abstract class AbstractControl extends Object implements IControl {
		use LazyConverterManagerTrait;
		use ConfigurableTrait;
		/**
		 * @var INode
		 */
		protected $node;

		/**
		 * @return INode
		 */
		public function getNode(): INode {
			return $this->node;
		}

		/**
		 * @inheritdoc
		 */
		public function addControl(IControl $control): IControl {
			$this->node->addNode($control->getNode(), true);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function addControlList(array $controlList): IControl {
			foreach ($controlList as $control) {
				$this->addControl($control);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getControlList(): array {
			$controlList = [];
			foreach ($this->node->getNodeList() as $node) {
				$controlList[] = $node->getMeta('control');
			}
			return $controlList;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function traverse(bool $self = true) {
			foreach (NodeIterator::recursive($this->node, $self) as $node) {
				yield $node->getMeta('control');
			}
		}

		protected function getContent(IElement $element, string $target = 'array') {
			if (($value = $element->getValue()) instanceof IContent) {
				return $this->converterManager->content($value, [$target])->convert()->getContent();
			}
			return null;
		}

		/**
		 * @inheritdoc
		 */
		protected function handleInit() {
			parent::handleInit();
			$this->node = new Node();
			$this->node->setMeta('control', $this);
		}
	}
