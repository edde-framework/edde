<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity\Query;

		use Edde\Api\Entity\IEntity;

		interface ILinkQuery extends IUnlinkQuery {
			/**
			 * right side of the link
			 *
			 * @return IEntity
			 */
			public function getTo(): IEntity;
		}
