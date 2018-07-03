<?php
	declare(strict_types=1);

	namespace Edde\Api\Schema;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Node\INode;

	/**
	 * General way how to handle schemas.
	 */
	interface ISchemaManager extends IConfigurable {
		/**
		 * register schema loader
		 *
		 * @param ISchemaLoader $schemaLoader
		 *
		 * @return ISchemaManager
		 */
		public function registerSchemaLoader(ISchemaLoader $schemaLoader): ISchemaManager;

		/**
		 * create schema from the given node
		 *
		 * @param INode $node
		 *
		 * @return ISchema
		 */
		public function createSchema(INode $node): ISchema;

		/**
		 * retrieve schema based on the name
		 *
		 * @param string $name
		 *
		 * @return ISchema
		 */
		public function getSchema(string $name): ISchema;

		/**
		 * return list of all available schemas
		 *
		 * @return ISchema[]
		 */
		public function getSchemaList(): array;
	}
