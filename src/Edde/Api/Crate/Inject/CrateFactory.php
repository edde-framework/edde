<?php
	declare(strict_types=1);

	namespace Edde\Api\Crate\Inject;

	use Edde\Api\Crate\ICrateFactory;

	/**
	 * Lazy crate cache dependency.
	 */
	trait CrateFactory {
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;

		/**
		 * @param ICrateFactory $crateFactory
		 */
		public function lazyCrateFactory(ICrateFactory $crateFactory) {
			$this->crateFactory = $crateFactory;
		}
	}
