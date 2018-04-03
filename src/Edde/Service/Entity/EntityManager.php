<?php
	declare(strict_types=1);
	namespace Edde\Service\Entity;

	use Edde\Entity\IEntityManager;

	trait EntityManager {
		/** @var IEntityManager */
		protected $entityManager;

		/**
		 * @param IEntityManager $entityManager
		 */
		public function injectEntityManager(IEntityManager $entityManager) {
			$this->entityManager = $entityManager;
		}
	}
