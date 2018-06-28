<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\TestCase;

	class StringFilterTest extends TestCase {
		public function testInput() {
			$filter = new StringFilter();
			self::assertSame('3.14', $filter->input(3.14));
			self::assertSame('', $filter->input(null));
			self::assertSame('prd', $filter->input(new class() {
				public function __toString() {
					return 'prd';
				}
			}));
		}

		public function testOutput() {
			$filter = new StringFilter();
			self::assertSame('3.14', $filter->output(3.14));
			self::assertSame('', $filter->output(null));
			self::assertSame('prd', $filter->output(new class() {
				public function __toString() {
					return 'prd';
				}
			}));
		}
	}
