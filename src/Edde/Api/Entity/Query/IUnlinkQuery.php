<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity\Query;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Schema\ILink;
		use Edde\Api\Storage\Query\IQuery;

		interface IUnlinkQuery extends IQuery {
			/**
			 * @return IEntity
			 */
			public function getEntity(): IEntity;

			/**
			 * @return ILink
			 */
			public function getLink(): ILink;
		}
