<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use DateTime;
	use Edde\TestCase;
	use Exception;

	class DateTimeFilterTest extends TestCase {
		/**
		 * @throws FilterException
		 * @throws Exception
		 */
		public function testDateTimeInput() {
			$filter = new DateTimeFilter();
			$stamp = new DateTime('2018-05-22T23:42:11');
			self::assertSame($stamp, $filter->input($stamp));
			self::assertInstanceOf(DateTime::class, $filter->input('2018-05-22T23:42:11'));
			self::assertNull($filter->input(null));
		}

		/**
		 * @throws FilterException
		 * @throws Exception
		 */
		public function testDateTimeOutput() {
			$filter = new DateTimeFilter();
			$stamp = new DateTime('2018-05-22T23:42:11.129394Z');
			self::assertSame('2018-05-22T23:42:11+0000', $filter->output($stamp));
			self::assertSame('2018-05-22T23:42:11+0000', $filter->output('2018-05-22T23:42:11'));
			self::assertNull($filter->output(null));
		}
	}
