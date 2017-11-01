<?php
	declare(strict_types=1);
	namespace Edde\Api\Schema;

		interface ILink {
			/**
			 * get source schema of the link
			 *
			 * @return ISchema
			 */
			public function getSourceSchema(): ISchema;

			/**
			 * get target schema of the link
			 *
			 * @return ISchema
			 */
			public function getTargetSchema(): ISchema;

			/**
			 * get source property (source schema -> source property)
			 *
			 * @return IProperty
			 */
			public function getSourceProperty(): IProperty;

			/**
			 * get target property (target schema -> target property)
			 *
			 * @return IProperty
			 */
			public function getTargetProperty(): IProperty;
		}
