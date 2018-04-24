<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	interface ISchema {
		/**
		 * return name of a schema (it could have even "namespace" like name)
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * return a primary attribute of this schema
		 *
		 * @return IAttribute
		 */
		public function getPrimary(): IAttribute;

		/**
		 * has this schema an alias?
		 *
		 * @return bool
		 */
		public function hasAlias(): bool;

		/**
		 * get alias of this schema
		 *
		 * @return string
		 */
		public function getAlias(): string;

		/**
		 * return alias or the original schema name
		 *
		 * @return string
		 */
		public function getRealName(): string;

		/**
		 * return meta attribute of the schema
		 *
		 * @param string $name
		 * @param null   $default
		 *
		 * @return mixed
		 */
		public function getMeta(string $name, $default = null);

		/**
		 * @param string $name
		 *
		 * @return IAttribute
		 *
		 * @throws SchemaException
		 */
		public function getAttribute(string $name): IAttribute;

		/**
		 * return list of attributes of this schema
		 *
		 * @return IAttribute[]
		 */
		public function getAttributes(): array;

		/**
		 * return list of unique properties
		 *
		 * @return IAttribute[]
		 */
		public function getUniques(): array;
	}
