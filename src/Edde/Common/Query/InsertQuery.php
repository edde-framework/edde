<?php
	namespace Edde\Common\Query;

		use Edde\Api\Query\IInsertQuery;
		use Edde\Common\Node\Node;

		class InsertQuery extends AbstractQuery implements IInsertQuery {
			/**
			 * @var string
			 */
			protected $table;
			/**
			 * @var array
			 */
			protected $source;
			/**
			 * @var string
			 */
			protected $type;

			public function __construct(string $table, array $source) {
				$this->table = $table;
				$this->source = $source;
				/**
				 * type in constructor is missing intentionally as it's not allowed for user to change this variable
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
				$this->node = new Node($this->type, null, ['table' => $this->table]);
				$parameterList = $this->node->getNode('parameter-list');
				$setList = $this->node->getNode('column-list');
				foreach ($this->source as $k => $v) {
					$setList->addNode(new Node('set', null, [
						'column'    => $k,
						'parameter' => $parameterId = ('p' . sha1($k . microtime(true) . random_bytes(64))),
					]));
					$parameterList->addNode(new Node('parameter', $v, ['name' => $parameterId]));
				}
			}
		}
