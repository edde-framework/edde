<?php
	declare(strict_types=1);
	namespace Edde\Service\Hydrator;

	use Edde\Hydrator\IHydrateManager;

	trait HydrateManager {
		/** @var IHydrateManager */
		protected $hydrateManager;

		/**
		 * @param IHydrateManager $hydrateManager
		 */
		public function injectHydrateManager(IHydrateManager $hydrateManager): void {
			$this->hydrateManager = $hydrateManager;
		}
	}
