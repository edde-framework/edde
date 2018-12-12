<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use DateTime;
	use Edde\TestCase;

	class StampFilterTest extends TestCase {
		/**
		 * @throws FilterException
		 */
		public function testInput() {
			$filter = new StampFilter();
			self::assertSame('foo', $filter->input('foo'));
		}

		/**
		 * @throws FilterException
		 */
		public function testOutput() {
			$filter = new StampFilter();
			$stamp = new DateTime();
			self::assertInstanceOf(DateTime::class, $filter->output(null));
			self::assertSame($stamp, $filter->output($stamp));
		}
	}
