<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\TestCase;

	class FloatFilterTest extends TestCase {
		public function testFloatFilterInput() {
			$filter = new FloatFilter();
			self::assertEquals(3.14, $filter->input('3.14'));
			self::assertNull($filter->input(null));
		}

		public function testFloatFilterOutput() {
			$filter = new FloatFilter();
			self::assertEquals(3.14, $filter->output('3.14'));
			self::assertNull($filter->output(null));
		}
	}
