<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		interface ILinkQuery extends IUnlinkQuery {
			/**
			 * right side of the link
			 *
			 * @return IEntity
			 */
			public function getTo(): IEntity;
		}
