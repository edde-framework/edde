<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use DateTime;
	use Edde\TestCase;

	class DateTimeFilterTest extends TestCase {
		public function testDateTimeInput() {
			$filter = new DateTimeFilter();
			$stamp = new DateTime('2018-05-22T23:42:11.129394');
			self::assertSame('2018-05-22 23:42:11.129394', $filter->input($stamp));
			self::assertSame('2018-05-22 23:42:11.000000', $filter->input('2018-05-22T23:42:11'));
			self::assertNull($filter->input(null));
		}

		public function testDateTimeOutput() {
			$filter = new DateTimeFilter();
			$stamp = new DateTime('2018-05-22T23:42:11');
			self::assertSame($stamp, $filter->output($stamp));
			self::assertInstanceOf(DateTime::class, $filter->output('2018-05-22T23:42:11'));
			self::assertNull($filter->output(null));
		}
	}
