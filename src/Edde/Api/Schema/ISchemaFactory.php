<?php
	declare(strict_types = 1);

	namespace Edde\Api\Schema;

	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Node\INode;

	/**
	 * Simple way how to load and build schemas from abstract source.
	 */
	interface ISchemaFactory extends IDeffered {
		/**
		 * add a schema node
		 *
		 * @param INode $node
		 *
		 * @return $this
		 */
		public function addSchemaNode(INode $node);

		/**
		 * load the specific file as INode and add it to this cache
		 *
		 * @param string $file
		 *
		 * @return INode
		 */
		public function load(string $file): INode;

		/**
		 * create list of schemas based on a given schema nodes
		 *
		 * @return ISchema[]
		 */
		public function create();
	}
