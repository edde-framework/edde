<?php
	namespace Edde\Api\Crypt\Inject;

		use Edde\Api\Crypt\IRandomService;

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
