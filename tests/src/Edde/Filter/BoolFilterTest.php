<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\TestCase;

	class BoolFilterTest extends TestCase {
		public function testBoolFilterInput() {
			$filter = new BoolFilter();
			self::assertTrue($filter->input('true'));
			self::assertTrue($filter->input(1));
			self::assertTrue($filter->input('on'));
			self::assertFalse($filter->input('false'));
			self::assertFalse($filter->input('off'));
			self::assertFalse($filter->input(0));
			self::assertTrue($filter->output('true'));
			self::assertTrue($filter->output(1));
			self::assertTrue($filter->output('on'));
			self::assertFalse($filter->output('false'));
			self::assertFalse($filter->output('off'));
			self::assertFalse($filter->output(0));
		}

		public function testBoolFilterOutput() {
			$filter = new BoolFilter();
			self::assertTrue($filter->output('true'));
			self::assertTrue($filter->output(1));
			self::assertTrue($filter->output('on'));
			self::assertFalse($filter->output('false'));
			self::assertFalse($filter->output('off'));
			self::assertFalse($filter->output(0));
		}
	}
