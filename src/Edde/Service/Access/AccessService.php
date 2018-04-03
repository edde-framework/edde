<?php
	declare(strict_types=1);
	namespace Edde\Service\Access;

	use Edde\Access\IAccessService;

	trait AccessService {
		/** @var IAccessService */
		protected $accessService;

		/**
		 * @param IAccessService $accessService
		 */
		public function injectAccessService(IAccessService $accessService): void {
			$this->accessService = $accessService;
		}
	}
