<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\LazyTemplateDirectoryTrait;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Node\SkipException;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\AbstractMacro;

	class SnippetMacro extends AbstractMacro {
		use LazyTemplateDirectoryTrait;

		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ICompiler $compiler, \Iterator $iterator, INode $node, string $name, $value = null) {
			$source->on(self::EVENT_PRE_ENTER, function () use ($compiler, $iterator, $node, $value) {
				ob_start();
				$iterator->next();
				$this->traverse($node, $iterator, $compiler);
				$this->templateDirectory->save($this->getSnippetFile($node, $value), ob_get_clean());
				throw new SkipException();
			});
		}

		/**
		 * @inheritdoc
		 */
		public function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			ob_start();
		}

		/**
		 * @inheritdoc
		 */
		public function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			$this->templateDirectory->save($this->getSnippetFile($node), ob_get_clean());
		}

		/**
		 * compute snippet filename
		 *
		 * @param INode       $node
		 * @param string|null $name
		 *
		 * @return string
		 * @throws MacroException
		 */
		protected function getSnippetFile(INode $node, string $name = null): string {
			return StringUtils::webalize($name ?? (string)$this->attribute($node, 'name')) . '.php';
		}
	}
