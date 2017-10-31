<?php
	namespace Edde\Api\Schema;

		interface ILink {
			/**
			 * return target schema
			 *
			 * @return ISchema
			 */
			public function getSchema(): ISchema;

			/**
			 * return property of the target schema
			 *
			 * @return string
			 */
			public function getTarget(): string;

			/**
			 * get source property name
			 *
			 * @return string
			 */
			public function getSource(): string;
		}
