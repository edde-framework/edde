<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\TestCase;

	class IntFilterTest extends TestCase {
		/**
		 * @throws FilterException
		 */
		public function testIntFilterInput() {
			$filter = new IntFilter();
			self::assertEquals(15000, $filter->input('15000'));
			self::assertEquals(0, $filter->input(null));
		}

		/**
		 * @throws FilterException
		 */
		public function testIntFilterOutput() {
			$filter = new IntFilter();
			self::assertEquals(15000, $filter->output('15000'));
			self::assertEquals(0, $filter->output(null));
		}
	}
