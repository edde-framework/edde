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
		public function testGetFilterException() {
			$this->expectException(FilterException::class);
			$this->expectExceptionMessage('Requested unknown filter [nope].');
			$this->filterManager->getFilter('nope');
		}
	}
