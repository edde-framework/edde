<?php
	declare(strict_types=1);
	namespace Edde\Api\Schema;

		interface ILink {
			/**
			 * get a from source schema/property
			 *
			 * @return ITarget
			 */
			public function getFrom(): ITarget;

			/**
			 * get a target target schema/property
			 *
			 * @return ITarget
			 */
			public function getTo(): ITarget;
		}
