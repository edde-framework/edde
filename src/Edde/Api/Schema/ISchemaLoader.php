<?php
	declare(strict_types=1);

	namespace Edde\Api\Schema;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Node\INode;

	/**
	 * Schema provider should provide schema definition in node.
	 */
	interface ISchemaLoader extends IConfigurable {
		/**
		 * @return INode[]|\Traversable
		 */
		public function load();
	}
