<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\Service\Filter\FilterManager;
	use Edde\TestCase;

	class FilterManagerTest extends TestCase {
		use FilterManager;

		/**
		 * @throws FilterException
		 */
		public function testBoolIntFilter() {
			$filter = $this->filterManager->getFilter('bool-int');
			self::assertEquals(1, $filter->input(true));
			self::assertEquals(0, $filter->input(false));
			self::assertEquals(0, $filter->input(null));
			self::assertEquals(0, $filter->input('off'));
			self::assertEquals(1, $filter->input('on'));
		}

		/**
		 * @throws FilterException
		 */
		public function testBoolFilter() {
			$filter = $this->filterManager->getFilter('bool');
			self::assertTrue($filter->input('on'));
			self::assertFalse($filter->output('off'));
			self::assertFalse($filter->output(0));
			self::assertFalse($filter->output(null));
			self::assertTrue($filter->input(true));
		}
	}
