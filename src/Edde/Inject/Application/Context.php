<?php
	declare(strict_types=1);
	namespace Edde\Inject\Application;

	use Edde\Api\Application\IContext;

	trait Context {
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
