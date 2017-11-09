<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Entity\IEntityQueue;

		interface IEntityQueueQuery extends IQuery {
			/**
			 * @return IEntityQueue
			 */
			public function getEntityQueue(): IEntityQueue;
		}
