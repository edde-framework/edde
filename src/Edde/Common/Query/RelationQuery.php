<?php
	namespace Edde\Common\Query;

		use Edde\Common\Node\Node;

		class RelationQuery extends AbstractQuery {
			/**
			 * @var string
			 */
			protected $relation;

			/**
			 * @param string $relation
			 */
			public function __construct(string $relation) {
				$this->relation = $relation;
			}

			public function addRelation(string $schema, string $property, string $value): RelationQuery {
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

			public function set(string $property, $value): RelationQuery {
				$this->init();
				$this->node->getNode('set-list')->addNode(new Node('set', null, [$property => $value]));
				return $this;
			}

			protected function handleInit(): void {
				parent::handleInit();
				$this->node = new Node('relation', null, ['name' => $this->relation]);
			}
		}
