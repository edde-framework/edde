<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IHelperSet;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\MacroException;
	use Edde\Common\AbstractObject;
	use Edde\Common\Deffered\DefferedTrait;
	use Edde\Common\Node\Node;

	/**
	 * Base macro for all template macros.
	 */
	abstract class AbstractMacro extends AbstractObject implements IMacro, ILazyInject {
		use DefferedTrait;
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var IHelperSet
		 */
		protected $helperSet;

		/**
		 * A master was explaining the nature of Tao to one of his novices. "The Tao is embodied in all software--regardless of how insignificant," said the master.
		 *
		 * "Is the Tao in the Unix command line?" asked the novice.
		 *
		 * "It is difficult to find, young one, but it is certainly there." came the reply.
		 *
		 * "Is Tao in a hand-held calculator?" asked the novice.
		 *
		 * "It is," came the reply.
		 *
		 * "Is the Tao in a video game?" continued the novice.
		 *
		 * "It is even in a video game," said the master.
		 *
		 * "What about MS-DOS?"
		 *
		 * The master coughed and shifted his position slightly. "The lesson is over for today," he said.
		 *
		 * @param string $name
		 *
		 * @internal param bool $compile
		 */
		public function __construct(string $name) {
			$this->name = $name;
		}

		/**
		 * @inheritdoc
		 */
		public function hasHelperSet(): bool {
			$this->use();
			return $this->helperSet !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function getHelperSet(): IHelperSet {
			$this->use();
			return $this->helperSet;
		}

		/**
		 * @inheritdoc
		 */
		public function inline(INode $macro, ICompiler $compiler) {
		}

		/**
		 * @inheritdoc
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			foreach ($macro->getNodeList() as $node) {
				$compiler->compile($node);
			}
		}

		/**
		 * @inheritdoc
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			foreach ($macro->getNodeList() as $node) {
				$compiler->macro($node);
			}
		}

		/**
		 * include node when root, otherwise switch node
		 *
		 * @param INode $macro
		 * @param string $attribute
		 *
		 * @return INode|Node
		 */
		protected function switchlude(INode $macro, string $attribute) {
			if ($macro->isRoot()) {
				return $this->insert($macro, $attribute);
			}
			return $this->switchNode($macro, $attribute);
		}

		/**
		 * insert node under the given macro
		 *
		 * @param INode $macro
		 * @param string $attribute
		 *
		 * @return Node
		 */
		protected function insert(INode $macro, string $attribute) {
			$macro->insert($node = new Node($this->getName(), null, [$attribute => $this->extract($macro, 't:' . $this->getName())]));
			return $node;
		}

		/**
		 * @inheritdoc
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * extract an attribute and remove it from attribute list
		 *
		 * @param INode $macro
		 * @param string $name
		 * @param null $default
		 *
		 * @return mixed|null|string
		 */
		public function extract(INode $macro, string $name = null, $default = null) {
			$name = $name ?: $this->getName();
			$attribute = $macro->getAttribute($name, $default);
			$macro->removeAttribute($name);
			return $attribute;
		}

		/**
		 * switch macro and node and extract attribute from macro node
		 *
		 * @param INode $macro
		 * @param string $attribute
		 *
		 * @return INode
		 */
		protected function switchNode(INode $macro, string $attribute): INode {
			$macro->switch($node = new Node($this->getName(), null, [$attribute => $this->extract($macro, 't:' . $this->getName())]));
			return $node;
		}

		/**
		 * return attribute from the given macro; throws exception if the attribute is not present
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 * @param string|null $name
		 * @param bool $helper
		 *
		 * @return mixed
		 * @throws MacroException
		 */
		protected function attribute(INode $macro, ICompiler $compiler, string $name = null, bool $helper = true) {
			$name = $name ?: $this->getName();
			if (($attribute = $macro->getAttribute($name)) === null) {
				throw new MacroException(sprintf('Missing attribute [%s] in macro node [%s].', $name, $macro->getPath()));
			}
			return ($helper && $filter = $compiler->helper($macro, $attribute)) ? $filter : $attribute;
		}

		/**
		 * return attribute list
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 * @param callable|null $default
		 *
		 * @return array
		 */
		protected function getAttributeList(INode $macro, ICompiler $compiler, callable $default = null): array {
			$attributeList = [];
			foreach ($macro->getAttributeList() as $k => &$v) {
				$v = ($value = $compiler->helper($macro, $v)) !== null ? $value : ($default ? $default($v) : $v);
				$attributeList[$k] = $v;
			}
			unset($v);
			return $attributeList;
		}
	}
