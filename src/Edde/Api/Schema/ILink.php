<?php
	declare(strict_types=1);
	namespace Edde\Api\Schema;

		interface ILink {
			/**
			 * schema of a link contains name of a link and could
			 * contain link properties
			 *
			 * @return ISchema
			 */
			public function getSchema(): ISchema;

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
