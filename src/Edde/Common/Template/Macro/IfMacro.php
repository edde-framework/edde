<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Common\Template\AbstractMacro;

	class IfMacro extends AbstractMacro {
		/**
		 * @inheritdoc
		 */
		public function getNameList(): array {
			return [
				'if',
				'inner-if',
			];
		}

		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ICompiler $compiler, \Iterator $iterator, INode $node, string $name, $value = null) {
			$events = $name === 'if' ? [
				self::EVENT_PRE_ENTER,
				self::EVENT_POST_LEAVE,
			] : [
				self::EVENT_POST_ENTER,
				self::EVENT_PRE_LEAVE,
			];
			$source->on($events[0], function () use ($value) {
				$this->open($value);
			});
			$source->on($events[1], function () {
				$this->close();
			});
		}

		/**
		 * @inheritdoc
		 */
		protected function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			$this->open($node->getAttribute('src'));
		}

		/**
		 * @inheritdoc
		 */
		protected function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			$this->close();
		}

		protected function open($value) {
			echo '<?php if(' . $this->delimite($value) . ') {?>';
		}

		protected function close() {
			echo '<?php } ?>';
		}
	}
