<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\LazyTemplateDirectoryTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Html\AbstractHtmlTemplate;

	/**
	 * Root control macro for template generation.
	 */
	class ControlMacro extends AbstractHtmlMacro {
		use LazyTemplateDirectoryTrait;

		/**
		 * Base 8 is just like base 10, if you are missing two fingers.
		 */
		public function __construct() {
			parent::__construct('control');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			if ($macro->isRoot() === false || $macro->getMeta('included', false)) {
				parent::macro($macro, $compiler);
				return null;
			}
			$this->use();
			$compiler->setVariable('file', $file = $this->templateDirectory->file(($class = 'Template_' . $compiler->getHash()) . '.php'));
			$file->openForWrite();
			$file->enableWriteCache();
			$this->write($compiler, '<?php');
			$this->write($compiler, "declare(strict_types = 1);\n", 1);
			$this->write($compiler, '/**', 1);
			$this->write($compiler, sprintf(' * @generated at %s', (new \DateTime())->format('Y-m-d H:i:s')), 1);
			$this->write($compiler, ' * automagically generated template file from the following source list:', 1);
			$nameList = $compiler->getImportList();
			foreach ($nameList as $name) {
				$this->write($compiler, sprintf(' *   - %s', $name), 1);
			}
			$this->write($compiler, ' */', 1);
			$this->write($compiler, sprintf('class %s extends %s {', $class, AbstractHtmlTemplate::class), 1);
			$this->write($compiler, sprintf("public function snippet(%s \$root, string \$snippet = null): %s {", IHtmlControl::class, IHtmlControl::class), 2);
			$this->write($compiler, '$this->embedd($this);', 3);
			$this->write($compiler, sprintf("\$stack = new SplStack();
			\$stack->push(\$control = \$parent = \$root);
			switch (\$snippet) {
				case null:", TemplateException::class), 3);
			$this->writeTextValue($macro, $compiler);
			$this->writeAttributeList($macro, $compiler);
			parent::macro($macro, $compiler);
			$this->write($compiler, 'break;', 5);
			$caseList = $compiler->getVariable($caseListId = (static::class . '/cast-list'), [null => null]);
			/** @var $nodeList INode[] */
			foreach ($compiler->getBlockList() as $id => $root) {
				if (isset($caseList[$id])) {
					continue;
				}
				$caseList[$id] = $id;
				/** @noinspection DisconnectedForeachInstructionInspection */
				$compiler->setVariable($caseListId, $caseList);
				$this->write($compiler, sprintf('case %s:', var_export($id, true)), 4);
				foreach ($root->getNodeList() as $node) {
					if ($node->getMeta('snippet', false)) {
						continue;
					}
					$this->write($compiler, sprintf('// %s', $node->getPath()), 5);
					$compiler->macro($node);
				}
				/** @noinspection DisconnectedForeachInstructionInspection */
				$this->write($compiler, 'break;', 5);
			}
			$this->write($compiler, sprintf("default:
					throw new %s(sprintf('Requested unknown snippet [%%s].', \$snippet));
			}", TemplateException::class), 4);
			$this->write($compiler, "return \$root;", 3);
			$this->write($compiler, '}', 2);
			$this->write($compiler, '');
			$this->write($compiler, 'public function getBlockList(): array {', 2);
			unset($caseList[null]);
			$this->write($compiler, 'return ' . var_export(array_keys($caseList), true) . ';', 3);
			$this->write($compiler, '}', 2);
			$this->write($compiler, '}', 1);
			$file->close();
			return $file;
		}

		protected function prepare() {
			parent::prepare();
			$this->templateDirectory->create();
		}
	}
