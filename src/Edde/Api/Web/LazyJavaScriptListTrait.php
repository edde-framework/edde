<?php

	namespace Edde\Api\Web;

	trait LazyJavaScriptListTrait {
		/**
		 * @var IJavaScriptList
		 */
		protected $javaScriptList;

		/**
		 * @param IJavaScriptList $javaScriptList
		 */
		public function lazyJavaScriptList(IJavaScriptList $javaScriptList) {
			$this->javaScriptList = $javaScriptList;
		}
	}
