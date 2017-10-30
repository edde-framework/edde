<?php
	namespace Edde\Common\Query;

		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Node\Node;

		class RelationQuery extends AbstractQuery {
			/**
			 * @var ISchema
			 */
			protected $schema;
			protected $source;

			public function __construct(ISchema $schema) {
				$this->schema = $schema;
			}

			public function addRelation(ILink $link, string $value): RelationQuery {
				$this->init();
				$this->node->getNode('relation-list')->addNode(new Node('link', null, [
					/**
					 * target schema
					 */
					'schema'   => $schema,
					/**
					 * target schema's property
					 */
					'property' => $property,
					/**
					 * value of the target property
					 */
					'value'    => $value,
				]));
				return $this;
			}

			protected function handleInit(): void {
				parent::handleInit();
				$this->node = new Node('relation', $this->source, ['schema' => $this->schema]);
			}
		}
