<?php
	namespace Edde\Api\Storage\Inject;

		use Edde\Api\Storage\IEntityManager;

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
