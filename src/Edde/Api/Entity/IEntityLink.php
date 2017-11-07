<?php
	namespace Edde\Api\Entity;

		use Edde\Api\Schema\ILink;

		interface IEntityLink {
			/**
			 * left side of the link
			 *
			 * @return IEntity
			 */
			public function getFrom(): IEntity;

			/**
			 * right side of the link
			 *
			 * @return IEntity
			 */
			public function getTo(): IEntity;

			/**
			 * get the link between entities
			 *
			 * @return ILink
			 */
			public function getLink(): ILink;
		}
