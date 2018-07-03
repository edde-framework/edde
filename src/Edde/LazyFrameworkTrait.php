<?php
	declare(strict_types=1);

	namespace Edde;

	/**
	 * Lazy trait for Framework class.
	 */
	trait LazyFrameworkTrait {
		/**
		 * @var Framework
		 */
		protected $framework;

		/**
		 * @param Framework $framework
		 */
		public function lazyFramework(Framework $framework) {
			$this->framework = $framework;
		}
	}
