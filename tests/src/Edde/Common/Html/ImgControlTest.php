<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Common\Html\Tag\ImgControl;
	use phpunit\framework\TestCase;

	class ImgControlTest extends TestCase {
		public function testCommon() {
			$imgControl = new ImgControl();
			$imgControl->setSrc('/img/some-image.png');
			self::assertEquals("<img src=\"/img/some-image.png\">\n", $imgControl->render());
		}
	}
