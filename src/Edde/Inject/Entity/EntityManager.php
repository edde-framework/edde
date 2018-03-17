<?php
	declare(strict_types=1);
	namespace Edde\Inject\Entity;

	use Edde\Entity\IEntityManager;

	trait EntityManager {
		/**
		 * @var IEntityManager
		 */
		protected $entityManager;

		/**
		 * @param IEntityManager $entityManager
		 */
		public function lazyEntityManager(IEntityManager $entityManager) {
			$this->entityManager = $entityManager;
		}
	}
