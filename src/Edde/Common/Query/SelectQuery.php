<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Exception\QueryException;
		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Schema\ISchema;

		class SelectQuery extends AbstractQuery implements ISelectQuery {
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var string
			 */
			protected $alias;
			/**
			 * @var ITable[]
			 */
			protected $joinList = [];
			/**
			 * @var string
			 */
			protected $select;

			public function __construct(ISchema $schema, string $alias) {
				parent::__construct('SelectQuery');
				$this->schema = $schema;
				$this->select = $this->alias = $alias;
			}

			/**
			 * @inheritdoc
			 */
			public function join(string $schema, string $alias): ISelectQuery {
				$this->joinList[$alias] = $schema;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function select(string $alias): ISelectQuery {
				if (isset($this->joinList[$alias]) === false && $this->alias !== $alias) {
					throw new QueryException(sprintf('Cannot select unknown alias [%s]; choose select alias [%s] or one of joined aliases [%s].', $alias, $this->alias, implode(', ', array_keys($this->joinList))));
				}
				$this->select = $alias;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getSchema(): ISchema {
				return $this->schema;
			}

			/**
			 * @inheritdoc
			 */
			public function getAlias(): string {
				return $this->alias;
			}
		}
