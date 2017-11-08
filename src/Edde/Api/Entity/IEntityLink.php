<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		interface IEntityLink extends IEntityUnlink {
			/**
			 * right side of the link
			 *
			 * @return IEntity
			 */
			public function getTo(): IEntity;
		}
