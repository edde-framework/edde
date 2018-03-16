<?php
	declare(strict_types=1);
	namespace Edde\Inject\Filter;

	use Edde\Api\Filter\IFilterManager;

	trait FilterManager {
		/**
		 * @var IFilterManager
		 */
		protected $filterManager;

		/**
		 * @param IFilterManager $filterManager
		 */
		public function lazyFilterManager(IFilterManager $filterManager) {
			$this->filterManager = $filterManager;
		}
	}
