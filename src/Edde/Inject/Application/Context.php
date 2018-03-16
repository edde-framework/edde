<?php
	declare(strict_types=1);
	namespace Edde\Inject\Application;

	use Edde\Application\IContext;

	trait Context {
		/**
		 * @var \Edde\Application\IContext
		 */
		protected $context;

		/**
		 * @param \Edde\Application\IContext $context
		 */
		public function lazyContext(\Edde\Application\IContext $context) {
			$this->context = $context;
		}
	}
