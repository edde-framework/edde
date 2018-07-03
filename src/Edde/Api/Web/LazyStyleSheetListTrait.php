<?php

	namespace Edde\Api\Web;

	trait LazyStyleSheetListTrait {
		/**
		 * @var IStyleSheetList
		 */
		protected $styleSheetList;

		/**
		 * @param IStyleSheetList $styleSheetList
		 */
		public function lazyStyleSheetList(IStyleSheetList $styleSheetList) {
			$this->styleSheetList = $styleSheetList;
		}
	}
