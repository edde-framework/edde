<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	/**
	 * Lazy dependency for the global set of helpers.
	 */
	trait LazyHelperSetTrait {
		/**
		 * @var IHelperSet
		 */
		protected $helperSet;

		/**
		 * @param IHelperSet $helperSet
		 */
		public function lazyHelperSet(IHelperSet $helperSet) {
			$this->helperSet = $helperSet;
		}
	}
