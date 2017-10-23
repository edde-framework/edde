<?php
	namespace Edde\Api\Filter\Inject;

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
