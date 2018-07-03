<?php
	declare(strict_types = 1);

	namespace Edde\Common\Filter;

	use phpunit\framework\TestCase;

	class IntBoolFilterTest extends TestCase {
		public function testCommon() {
			$filter = new IntBoolFilter();
			self::assertEquals(1, $filter->filter('true'));
			self::assertEquals(1, $filter->filter('on'));
			self::assertEquals(1, $filter->filter('1'));
			self::assertEquals(1, $filter->filter(true));

			self::assertEquals(0, $filter->filter('false'));
			self::assertEquals(0, $filter->filter('off'));
			self::assertEquals(0, $filter->filter('0'));
			self::assertEquals(0, $filter->filter(false));
			self::assertEquals(0, $filter->filter(null));
		}
	}
