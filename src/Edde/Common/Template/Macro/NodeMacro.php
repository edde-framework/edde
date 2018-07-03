<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Common\Template\AbstractMacro;

	class NodeMacro extends AbstractMacro {
		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ICompiler $compiler, \Iterator $iterator, INode $node, string $name, $value = null) {
			$events = [
				self::EVENT_PRE_ENTER,
				self::EVENT_POST_LEAVE,
			];
			$source->on($events[0], function () {
				$this->macroOpen();
			});
			$source->on($events[1], function () use ($value) {
				$this->macroClose((string)$value);
			});
		}

		/**
		 * @inheritdoc
		 */
		protected function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			$this->macroOpen();
		}

		/**
		 * @inheritdoc
		 */
		protected function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			$this->macroClose($this->attribute($node, 'target'));
		}

		public function macroOpen() {
			echo '<?php ob_start(); ?>';
		}

		public function macroClose(string $value) {
			echo '<?php ';
			?>
			$node = $this->converterManager->convert(ob_get_clean(), 'string', [\Edde\Api\Node\INode::class])->convert()->getContent();
			<?php
			echo '$node = ' . str_replace('()', '($node);', $this->delimite($value, true)) . "\n";
			?>
			$this->htmlGenerator->render($node);
			<?php
			echo '?>';
		}
	}
