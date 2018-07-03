<?php
	declare(strict_types=1);

	namespace Edde\Api\Container\Inject;

	use Edde\Api\Container\IContainer;

	/**
	 * Defines lazy dependency on a system dependency container.
	 */
	trait Container {
		/**
		 * @var IContainer
		 */
		protected $container;

		/**
		 * @param IContainer $container
		 */
		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}
	}
