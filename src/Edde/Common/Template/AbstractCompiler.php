<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\ITreeTraversal;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Node\AbstractTreeTraversal;

	abstract class AbstractCompiler extends AbstractTreeTraversal implements ICompiler {
		use ConfigurableTrait;
		/**
		 * @var IMacro[]
		 */
		protected $macroList;

		/**
		 * @inheritdoc
		 */
		public function registerMacro(string $name, IMacro $macro): ICompiler {
			$this->macroList[$name] = $macro;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getMacro(string $name, INode $source): IMacro {
			if (isset($this->macroList[$name]) === false) {
				throw new MacroException(sprintf('Unknown macro [%s] on node [%s].', $name, $source->getPath()));
			}
			return $this->macroList[$name];
		}

		/**
		 * @inheritdoc
		 */
		public function select(INode $node, ...$parameters): ITreeTraversal {
			/** @var $compiler ICompiler */
			list($compiler) = $parameters;
			return $compiler->getMacro($node->getName(), $node);
		}
	}
