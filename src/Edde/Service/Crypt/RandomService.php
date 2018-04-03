<?php
	declare(strict_types=1);
	namespace Edde\Service\Crypt;

	use Edde\Crypt\IRandomService;

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
