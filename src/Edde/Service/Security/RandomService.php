<?php
	declare(strict_types=1);
	namespace Edde\Service\Security;

	use Edde\Security\IRandomService;

	trait RandomService {
		/** @var IRandomService */
		protected $randomService;

		/**
		 * @param IRandomService $randomService
		 */
		public function injectRandomService(IRandomService $randomService) {
			$this->randomService = $randomService;
		}
	}
