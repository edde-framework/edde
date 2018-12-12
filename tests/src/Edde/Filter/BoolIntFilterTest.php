<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\TestCase;

	class BoolIntFilterTest extends TestCase {
		/**
		 * @throws FilterException
		 */
		public function testBoolFilterInput() {
			$filter = new BoolIntFilter();
			self::assertTrue($filter->input('true'));
			self::assertTrue($filter->input(1));
			self::assertTrue($filter->input('on'));
			self::assertFalse($filter->input('false'));
			self::assertFalse($filter->input('off'));
			self::assertFalse($filter->input(0));
		}

		/**
		 * @throws FilterException
		 */
		public function testBoolFilterOutput() {
			$filter = new BoolIntFilter();
			self::assertSame(1, $filter->output('true'));
			self::assertSame(1, $filter->output(1));
			self::assertSame(1, $filter->output('on'));
			self::assertSame(0, $filter->output('false'));
			self::assertSame(0, $filter->output('off'));
			self::assertSame(0, $filter->output(0));
		}
	}
