<?php
	namespace Edde\Common\Query;

		use Edde\Common\Node\Node;
		use Edde\Common\Query\Fragment\WhereFragment;

		class UpdateQuery extends InsertQuery {
			/**
			 * @var string
			 */
			protected $table;
			/**
			 * @var array
			 */
			protected $source;

			public function __construct(string $table, array $source) {
				parent::__construct($table, $source);
				$this->type = 'update';
			}

			public function where(): WhereFragment {
				$this->init();
				$this->node->getNode('where-list')->addNode($node = new Node('where'));
				return new WhereFragment($this->node, $node);
			}
		}
