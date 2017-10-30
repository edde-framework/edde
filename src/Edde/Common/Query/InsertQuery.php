<?php
	namespace Edde\Common\Query;

		use Edde\Api\Query\IInsertQuery;
		use Edde\Common\Node\Node;

		class InsertQuery extends AbstractQuery implements IInsertQuery {
			/**
			 * @var string
			 */
			protected $name;
			/**
			 * @var array
			 */
			protected $source;
			/**
			 * @var string
			 */
			protected $type;

			public function __construct(string $table, array $source) {
				$this->name = $table;
				$this->source = $source;
				/**
				 * type in constructor is missing intentionally as user is not allowed to change this variable
				 */
				$this->type = 'insert';
			}

			public function alias(string $alias): IInsertQuery {
				$this->init();
				$this->node->setAttribute('alias', $alias);
				return $this;
			}

			/**
			 * @throws \Exception
			 */
			protected function handleInit(): void {
				parent::handleInit();
				$this->node = new Node($this->type, null, ['name' => $this->name]);
				$this->node->getNode('set-list')->addNode(new Node('set', null, $this->source));
			}
		}
