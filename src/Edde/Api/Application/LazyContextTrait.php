<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	trait LazyContextTrait {
		/**
		 * @var IContext
		 */
		protected $context;

		/**
		 * @param IContext $context
		 */
		public function lazyContext(IContext $context) {
			$this->context = $context;
		}
	}
