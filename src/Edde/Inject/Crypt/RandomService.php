<?php
	declare(strict_types=1);
	namespace Edde\Inject\Crypt;

	use Edde\Crypt\IRandomService;

	trait RandomService {
		/**
		 * @var \Edde\Crypt\IRandomService
		 */
		protected $randomService;

		/**
		 * @param \Edde\Crypt\IRandomService $randomService
		 */
		public function lazyRandomService(\Edde\Crypt\IRandomService $randomService) {
			$this->randomService = $randomService;
		}
	}
