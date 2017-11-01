<?php
	namespace Edde\Api\Entity\Inject;

		use Edde\Api\Entity\IEntityManager;

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
