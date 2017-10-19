<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Node\INode;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Object\Object;

		abstract class AbstractFragment extends Object {
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var INode
			 */
			protected $node;

			/**
			 * @param ISchema $schema
			 * @param INode   $node
			 */
			public function __construct(ISchema $schema, INode $node = null) {
				$this->schema = $schema;
				$this->node = $node;
			}

			public function getNode() {
				return $this->node;
			}
		}
