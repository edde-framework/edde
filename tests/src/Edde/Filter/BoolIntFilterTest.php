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
			self::assertSame(1, $filter->input('true'));
			self::assertSame(1, $filter->input(1));
			self::assertSame(1, $filter->input('on'));
			self::assertSame(0, $filter->input('false'));
			self::assertSame(0, $filter->input('off'));
			self::assertSame(0, $filter->input(0));
		}

		/**
		 * @throws FilterException
		 */
		public function testBoolFilterOutput() {
			$filter = new BoolIntFilter();
			self::assertTrue($filter->output('true'));
			self::assertTrue($filter->output(1));
			self::assertTrue($filter->output('on'));
			self::assertFalse($filter->output('false'));
			self::assertFalse($filter->output('off'));
			self::assertFalse($filter->output(0));
		}
	}
