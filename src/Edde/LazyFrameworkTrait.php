<?php
	declare(strict_types = 1);

	namespace Edde;

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
