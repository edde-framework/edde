<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Entity\IEntityQueue;
		use Edde\Api\Query\IEntityQueueQuery;

		class EntityQueueQuery extends AbstractQuery implements IEntityQueueQuery {
			/** @var IEntityQueue */
			protected $entityQueue;

			public function __construct(IEntityQueue $entityQueue) {
				$this->entityQueue = $entityQueue;
			}

			/**
			 * @inheritdoc
			 */
			public function getEntityQueue(): IEntityQueue {
				return $this->entityQueue;
			}
		}
