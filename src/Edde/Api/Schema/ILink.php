<?php
	namespace Edde\Api\Schema;

		interface ILink {
			/**
			 * return target schema name
			 *
			 * @return string
			 */
			public function getSchema(): string;

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
			public function getProperty(): string;
		}
