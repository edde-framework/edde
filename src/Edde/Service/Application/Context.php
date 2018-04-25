<?php
	declare(strict_types=1);
	namespace Edde\Service\Application;

	use Edde\Application\IContext;

	trait Context {
		/** @var IContext */
		protected $context;

		/**
		 * @param IContext $context
		 */
		public function injectContext(IContext $context) {
			$this->context = $context;
		}
	}
