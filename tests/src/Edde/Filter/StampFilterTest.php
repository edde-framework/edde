<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use DateTime;
	use Edde\TestCase;

	class StampFilterTest extends TestCase {
		public function testInput() {
			$filter = new StampFilter();
			$stamp = new DateTime();
			self::assertInstanceOf(DateTime::class, $filter->input(null));
			self::assertSame($stamp, $filter->input($stamp));
		}

		public function testOutput() {
			$filter = new StampFilter();
			self::assertSame('foo', $filter->output('foo'));
		}
	}
