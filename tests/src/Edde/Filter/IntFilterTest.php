<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\TestCase;

	class IntFilterTest extends TestCase {
		public function testIntFilterInput() {
			$filter = new IntFilter();
			self::assertEquals(15000, $filter->input('15000'));
		}

		public function testIntFilterOutput() {
			$filter = new IntFilter();
			self::assertEquals(15000, $filter->output('15000'));
		}
	}
