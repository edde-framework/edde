<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\ITreeTraversal;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Node\AbstractTreeTraversal;
	use Edde\Common\Strings\StringUtils;

	abstract class AbstractMacro extends AbstractTreeTraversal implements IMacro {
		const EVENT_PRE_ENTER = 0;
		const EVENT_POST_ENTER = 1;
		const EVENT_PRE_NODE = 2;
		const EVENT_POST_NODE = 3;
		const EVENT_PRE_LEAVE = 4;
		const EVENT_POST_LEAVE = 5;
		/**
		 * @var callable[]
		 */
		protected $eventList = [];
		protected $level = 0;

		/**
		 * @inheritdoc
		 */
		public function getNameList(): array {
			return [StringUtils::recamel(str_replace('Macro', '', StringUtils::extract(static::class, '\\', -1)))];
		}

		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ICompiler $compiler, \Iterator $iterator, INode $node, string $name, $value = null) {
		}

		/**
		 * @inheritdoc
		 */
		public function on($event, callable $callback): IMacro {
			$this->eventList[$this->level][$event][] = $callback;
			return $this;
		}

		protected function event($event) {
			foreach ($this->eventList[$this->level][$event] ?? [] as $callable) {
				$callable();
			}
		}

		/**
		 * @inheritdoc
		 */
		public function select(INode $node, ...$parameters): ITreeTraversal {
			/** @var $compiler ICompiler */
			list($compiler) = $parameters;
			return $compiler->getMacro($node->getName(), $node);
		}

		/**
		 * @inheritdoc
		 */
		public function enter(INode $node, \Iterator $iterator, ...$parameters) {
			$this->level++;
			/** @var $compiler ICompiler */
			list($compiler) = $parameters;
			$attributeList = $node->getAttributeList();
			$inlineList = $attributeList->get('t', []);
			$attributeList->remove('t');
			foreach ($inlineList as $name => $value) {
				$macro = $compiler->getMacro($name, $node);
				$macro->inline($this, $compiler, $iterator, $node, $name, $value);
			}
			$this->event(self::EVENT_PRE_ENTER);
			$this->onEnter($node, $iterator, ...$parameters);
			$this->event(self::EVENT_POST_ENTER);
		}

		/**
		 * @inheritdoc
		 */
		public function node(INode $node, \Iterator $iterator, ...$parameters) {
			$this->event(self::EVENT_PRE_NODE);
			$this->onNode($node, $iterator, ...$parameters);
			$this->event(self::EVENT_POST_NODE);
		}

		/**
		 * @inheritdoc
		 */
		public function leave(INode $node, \Iterator $iterator, ...$parameters) {
			$this->event(self::EVENT_PRE_LEAVE);
			$this->onLeave($node, $iterator, ...$parameters);
			$this->event(self::EVENT_POST_LEAVE);
			$this->eventList[$this->level] = [];
			$this->level--;
		}

		/**
		 * @inheritdoc
		 */
		public function register(ICompiler $compiler): IMacro {
			foreach ($this->getNameList() as $name) {
				$compiler->registerMacro($name, $this);
			}
			return $this;
		}

		protected function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
		}

		protected function onNode(INode $node, \Iterator $iterator, ...$parameters) {
			parent::node($node, $iterator, ...$parameters);
		}

		protected function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
		}

		protected function attribute(INode $node, string $attribute, bool $literal = false) {
			$this->checkAttribute($node, $attribute);
			return $this->delimite($node->getAttribute($attribute), $literal);
		}

		protected function checkAttribute(INode $node, string $attribute) {
			if ($node->hasAttribute($attribute) === false) {
				throw new MacroException(sprintf('Missing attribute <%s (%s=...)> in node [%s].', $node->getName(), $attribute, $node->getPath()));
			}
		}

		protected function delimite($value, bool $literal = false) {
			if ($value && ($method = StringUtils::match($value, '~^((?<context>[a-zA-Z0-9_\$-]+))?:(?<method>[a-zA-Z0-9_-]+)\((?<parameters>.*?)\)$~', true, true)) !== null) {
				return '$context[' . (isset($method['context']) ? "'" . $method['context'] . "'" : 'null') . ']->' . StringUtils::toCamelHump($method['method']) . '(' . ($method['parameters'] ?? '') . ')';
			}
			return $literal || $value[0] === '$' ? $value : var_export($value, true);
		}
	}
