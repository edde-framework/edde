<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity\Query;

		use Edde\Api\Entity\IEntityQueue;
		use Edde\Api\Storage\Query\IQuery;

		interface IQueryQueue extends IQuery {
			/**
			 * @return IEntityQueue
			 */
			public function getEntityQueue(): IEntityQueue;
		}
