<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		use Edde\Api\Schema\ILink;

		interface IUnlinkQuery {
			/**
			 * @return IEntity
			 */
			public function getEntity(): IEntity;

			/**
			 * @return ILink
			 */
			public function getLink(): ILink;
		}
