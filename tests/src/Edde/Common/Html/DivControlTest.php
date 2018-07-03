<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Html\HtmlException;
	use Edde\Common\Html\Tag\DivControl;
	use phpunit\framework\TestCase;

	class DivControlTest extends TestCase {
		public function testCommon() {
			$divControl = new DivControl();
			self::assertEquals("<div></div>\n", $divControl->render());
		}

		public function testDivClassList() {
			$divControl = new DivControl();
			$divControl->addClass('foo');
			$divControl->addClass('bar');
			self::assertEquals("<div class=\"foo bar\"></div>\n", $divControl->render());
		}

		public function testDivException() {
			$this->expectException(HtmlException::class);
			$this->expectExceptionMessage(sprintf('Cannot set tag [poo] to a [%s] control.', DivControl::class));
			$divControl = new DivControl();
			$divControl->setTag('poo');
		}

		public function testDivTree() {
			$divControl = new DivControl();
			$divControl->addClass('foo');
			$divControl->addClass('bar');
			$divControl->addControl((new DivControl())->addClass('one'));
			$divControl->dirty();
			self::assertEquals('<div class="foo bar">
	<div class="one"></div>
</div>
', $divControl->render());
		}
	}
