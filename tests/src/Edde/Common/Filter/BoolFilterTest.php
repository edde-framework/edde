<?php
	declare(strict_types = 1);

	namespace Edde\Common\Filter;

	use phpunit\framework\TestCase;

	class BoolFilterTest extends TestCase {
		public function testCommon() {
			$filter = new BoolFilter();
			self::assertTrue($filter->filter('true'));
			self::assertTrue($filter->filter('on'));
			self::assertTrue($filter->filter('1'));
			self::assertTrue($filter->filter(true));

			self::assertFalse($filter->filter('false'));
			self::assertFalse($filter->filter('off'));
			self::assertFalse($filter->filter('0'));
			self::assertFalse($filter->filter(false));
			self::assertFalse($filter->filter(null));
		}
	}
