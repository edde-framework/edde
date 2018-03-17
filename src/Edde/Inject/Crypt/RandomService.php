<?php
	declare(strict_types=1);
	namespace Edde\Inject\Crypt;

	use Edde\Crypt\IRandomService;

	trait RandomService {
		/**
		 * @var IRandomService
		 */
		protected $randomService;

		/**
		 * @param IRandomService $randomService
		 */
		public function lazyRandomService(IRandomService $randomService) {
			$this->randomService = $randomService;
		}
	}
