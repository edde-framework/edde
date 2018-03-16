<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity\Query;

	use Edde\Api\Entity\IEntity;
	use Edde\Api\Storage\Query\IQuery;
	use Edde\Schema\ILink;

	interface IUnlinkQuery extends IQuery {
		/**
		 * @return IEntity
		 */
		public function getEntity(): IEntity;

		/**
		 * @return \Edde\Schema\ILink
		 */
		public function getLink(): ILink;
	}
