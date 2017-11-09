<?php
	declare(strict_types=1);
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
			 * get target schema name (shortcut for getSchema()->getName())
			 *
			 * @return string
			 */
			public function getName(): string;

			/**
			 * shorthand to get schema real name
			 *
			 * @return string
			 */
			public function getRealName(): string;

			/**
			 * get target property
			 *
			 * @return IProperty
			 */
			public function getProperty(): IProperty;

			/**
			 * shortcut for a... property name!
			 *
			 * @return string
			 */
			public function getPropertyName(): string;
		}
