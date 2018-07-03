<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Common\Template\AbstractMacro;

	class TemplateMacro extends AbstractMacro {
		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ICompiler $compiler, \Iterator $iterator, INode $node, string $name, $value = null) {
			$source->on(self::EVENT_POST_ENTER, function () use ($node, $value) {
				$this->checkAttribute($node, 'template');
				$attribute = $node->getAttribute('template');
				$this->macro($value, $attribute->get('context'), $attribute->get('pass'));
			});
		}

		/**
		 * @inheritdoc
		 */
		protected function onNode(INode $node, \Iterator $iterator, ...$parameters) {
			$this->macro($this->attribute($node, 'name'), $this->attribute($node, 'context'), $node->getAttribute('pass'));
		}

		protected function macro($name, $context, $pass = null) {
			echo sprintf('<?php $instance = $this->container->create(%s, [], %s); ?>', $this->delimite($context), '\'Template macro: \'.' . ($name = $this->delimite($name)));
			if ($pass) {
				echo '<?php ' . str_replace('()', '($instance, ' . $name . ')', $this->delimite($pass)) . '; ?>';
			}
			echo '<?php $instance instanceof ' . IConfigurable::class . ' ? $instance->setup() : null; ?>';
			echo sprintf("<?php \$this->templateManager->template()->template(%s, \$instance, null, \$instance)->execute(); ?>", $name);
		}
	}
