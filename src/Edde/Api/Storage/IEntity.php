<?php
	namespace Edde\Api\Storage;

		use Edde\Api\Crate\ICrate;
		use Edde\Api\Crate\IProperty;
		use Edde\Api\Schema\ISchema;

		/**
		 * An Entity is extended Crate with some additional features.
		 */
		interface IEntity extends ICrate {
			/**
			 * get entity's schema
			 *
			 * @return ISchema
			 */
			public function getSchema(): ISchema;

			/**
			 * return list of primary properties
			 *
			 * @return IProperty[]
			 */
			public function getPrimaryList(): array;
		}
