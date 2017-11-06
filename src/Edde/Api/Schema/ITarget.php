<?php
	namespace Edde\Api\Schema;

	/**
	 * Target of a link.
	 */
		interface ITarget {
			/**
			 * get schema of a target
			 *
			 * @return ISchema
			 */
			public function getSchema(): ISchema;

			/**
			 * get target property
			 *
			 * @return IProperty
			 */
			public function getProperty(): IProperty;
		}
