<?php
	declare(strict_types = 1);

	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\Tag\SpanControl;

	class SomeCoolControl extends DivControl {
		/**
		 * @var DivControl
		 */
		public $someVariable;
		/**
		 * @var SpanControl
		 */
		public $spanControl;
		/**
		 * @var SpanControl
		 */
		public $includedVariable;
		/**
		 * @var DivControl
		 */
		public $overkillBlock;
		protected $trueVariableReference = true;
		protected $falseVariableReference = false;

		public function overkillBlock(DivControl $divControl) {
			$divControl->addClass('overkilled');
		}

		public function trueMethod() {
			return true;
		}

		public function falseMethod() {
			return false;
		}

		public function spanMethodCall(SpanControl $spanControl) {
			$this->spanControl = $spanControl;
		}

		public function passChild(DivControl $divControl) {
			$divControl->addClass('passed');
		}

		public function passChild2(DivControl $divControl) {
			$divControl->addClass('passed-02');
		}

		public function passChild3(DivControl $divControl) {
			$divControl->addClass('passed-03');
		}

		public function gimmeSomeIterator() {
			return [
				'class-a',
				'class-b',
				'class-c',
			];
		}

		public function loopFromRoot() {
			return [
				'a' => 'abc',
				'b' => 'def',
				'c' => 'ghi',
			];
		}

		public function loopOverLoopFromRoot() {
			for ($i = 0; $i < 3; $i++) {
				yield 'upper-loop-key' . $i => (function () {
					for ($i = 0; $i < 4; $i++) {
						yield 'inner-loop-key-' . $i => 'inner-loop-value-' . $i;
					}
				})();
			}
		}

		public function rootMethodCall() {
			return 'ou-yay!';
		}

		public function currentMethodCall() {
			return 'yahoo!';
		}

		public function middleLocalMethodCall() {
			$this->addControl($this->createControl(DivControl::class)
				->setText('cha!'));
		}

		public function middleRootMethodCall() {
			$this->addControl($this->createControl(DivControl::class)
				->setText('even bigger cha!'));
		}

		public function gimmeASwitch() {
			return 'foo';
		}
	}

	class AnotherCoolControl extends DivControl {
		public function loopFromLocalControl() {
			for ($i = 0; $i < 3; $i++) {
				yield 'foobarpoo-' . $i => new class($i) {
					protected $i;

					public function __construct($i) {
						$this->i = $i;
					}

					public function getClass() {
						return 'clazz-[' . $this->i . ']-here';
					}
				};
			}
		}
	}
