<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime\Event;

	use Edde\Api\Container\IContainer;

	/**
	 * Container is built, this is called before run method of runtime.
	 */
	class BootstrapEvent extends RuntimeEvent {
		/**
		 * @var IContainer
		 */
		protected $container;

		/**
		 * @param IContainer $container
		 */
		public function __construct(IContainer $container) {
			$this->container = $container;
		}

		/**
		 * @return IContainer
		 */
		public function getContainer(): IContainer {
			return $this->container;
		}
	}
