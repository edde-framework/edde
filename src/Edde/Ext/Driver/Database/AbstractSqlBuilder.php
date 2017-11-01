<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver\Database;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Api\Query\IInsertQuery;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Query\IUpdateQuery;
		use Edde\Common\Query\AbstractQueryBuilder;
		use Edde\Common\Query\NativeQuery;
		use Edde\Common\Query\NativeTransaction;

		abstract class AbstractSqlBuilder extends AbstractQueryBuilder {
			protected function fragmentCreateSchema(ICrateSchemaQuery $query): INativeTransaction {
				$schema = $query->getSchema();
				$sql = 'CREATE TABLE ' . ($this->delimite($table = $schema->getName())) . " (\n\t";
				$columnList = [];
				$primaryList = [];
				foreach ($schema->getPropertyList() as $property) {
					$column = ($name = $this->delimite($property->getName())) . ' ' . $this->type($property->getType());
					if ($property->isPrimary()) {
						$primaryList[] = $name;
					} else if ($property->isUnique()) {
						$column .= ' UNIQUE';
					}
					if ($property->isRequired()) {
						$column .= ' NOT NULL';
					}
					$columnList[] = $column;
				}
				if (empty($primaryList) === false) {
					$columnList[] = "CONSTRAINT " . $this->delimite(sha1($table . '_primary_' . $primary = implode(', ', $primaryList))) . ' PRIMARY KEY (' . $primary . ")\n";
				}
				return (new NativeTransaction())->query(new NativeQuery($sql . implode(",\n\t", $columnList) . "\n)"));
			}

			protected function fragmentInsert(IInsertQuery $insertQuery): INativeTransaction {
				$nameList = [];
				$parameterList = [];
				foreach ($insertQuery->getSource() as $k => $v) {
					$nameList[] = $this->delimite($k);
					$parameterList['p_' . sha1($k)] = $v;
				}
				$schema = $insertQuery->getSchema();
				$sql = 'INSERT INTO ' . $this->delimite($schema->getName()) . ' (' . implode(',', $nameList) . ') VALUES (';
				return (new NativeTransaction())->query(new NativeQuery($sql . ':' . implode(', :', array_keys($parameterList)) . ')', $parameterList));
			}

			/**
			 * @param IUpdateQuery $updateQuery
			 *
			 * @return INativeTransaction
			 */
			protected function fragmentUpdate(IUpdateQuery $updateQuery): INativeTransaction {
				$schema = $updateQuery->getSchema();
				$where = null;
				$setList = [];
				$parameterList = [];
				/**
				 * micro-optimization to eliminate array_merge over parameter list
				 */
				if ($updateQuery->hasWhere()) {
					$query = $this->fragmentWhereList($updateQuery->getWhere());
					$where .= "WHERE\n" . $query->getQuery();
					$parameterList = $query->getParameterList();
				}
				foreach ($updateQuery->getSource() as $k => $v) {
					$setList[] = $this->delimite($k) . ' = :' . $parameterId = ('p_' . sha1($k));
					$parameterList[$parameterId] = $v;
				}
				$sql = "UPDATE\n\t" . $this->delimite($schema->getName()) . ' ' . "\nSET\n\t";
				return (new NativeTransaction())->query(new NativeQuery($sql . implode(",\n\t", $setList) . "\n" . $where, $parameterList));
			}

			protected function fragmentSelect(ISelectQuery $selectQuery): INativeTransaction {
				$sql = "SELECT\n";
				foreach ($selectQuery->getSchemaFragmentList() as $schema) {
				}
				$parameterList = [];
				$query = $this->fragmentColumnList($root->getNode('column-list'));
				$sql[] = $query->getQuery();
				$parameterList = array_merge($parameterList, $query->getParameterList());
				if ($root->hasNode('table-list')) {
					$sql[] = "FROM\n";
					$query = $this->fragmentTableList($root->getNode('table-list'));
					$sql[] = $query->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				if ($root->hasNode('where-list')) {
					$sql[] = "WHERE\n";
					$query = $this->fragmentWhereList($root->getNode('where-list'));
					$sql[] = $query->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				if ($root->hasNode('order-list')) {
					$sql[] = "ORDER BY\n";
					$query = $this->fragmentOrderList($root->getNode('order-list'));
					$sql[] = $query->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				$parameterList = array_merge($parameterList, ($this->fragmentParameterList($root->getNode('parameter-list')))->getParameterList());
				return new NativeTransaction(implode('', $sql), $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeTransaction
			 */
			protected function fragmentColumnList(INode $root): INativeTransaction {
				$columnList = [];
				$parameterList = [];
				foreach ($root->getNodeList() as $node) {
					$query = $this->fragment($node);
					$columnList[] = "\t" . $query->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				return new NativeTransaction(implode(",\n", $columnList) . "\n", $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeTransaction
			 * @throws QueryBuilderException
			 */
			protected function fragmentColumn(INode $root): INativeTransaction {
				switch ($type = $root->getAttribute('type')) {
					case 'column':
						$column = ($prefix = $root->getAttribute('prefix')) ? $this->delimite($prefix) . '.' : '';
						$column .= $this->delimite($root->getValue());
						$column .= ($alis = $root->getAttribute('alias')) ? ' AS ' . $this->delimite($alis) : '';
						return new NativeTransaction($column);
					case 'asterisk':
						$column = ($prefix = $root->getAttribute('prefix')) ? $this->delimite($prefix) . '.' : '';
						$column .= '*';
						return new NativeTransaction($column);
				}
				throw new QueryBuilderException(sprintf('Unknown column type [%s].', $type));
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeTransaction
			 */
			protected function fragmentTableList(INode $root): INativeTransaction {
				$tableList = [];
				$parameterList = [];
				foreach ($root->getNodeList() as $node) {
					$query = $this->fragment($node);
					$tableList[] = "\t" . $query->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				return (new NativeTransaction())->query(new NativeQuery(implode(",\n", $tableList) . "\n", $parameterList));
			}

			protected function fragmentTable(INode $root): INativeTransaction {
				$table = $this->delimite($root->getAttribute('table'));
				$table .= (($alias = $root->getAttribute('alias')) ? ' ' . $this->delimite($alias) : '');
				return (new NativeTransaction())->query(new NativeQuery($table));
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeTransaction
			 * @throws QueryBuilderException
			 * @throws \Exception
			 */
			protected function fragmentWhere(INode $root): INativeTransaction {
				static $expressions = [
					'eq'  => '=',
					'neq' => '!=',
					'gt'  => '>',
					'gte' => '>=',
					'lt'  => '<',
					'lte' => '<=',
				];
				$parameterList = [];
				if (isset($expressions[$type = $root->getAttribute('type')])) {
					$where = ($prefix = $root->getAttribute('prefix')) ? $this->delimite($prefix) . '.' : '';
					$where .= $this->delimite($root->getAttribute('where')) . ' ' . $expressions[$type] . ' ';
					switch ($target = $root->getAttribute('target', 'column')) {
						case 'column':
							$where .= ($prefix = $root->getAttribute('column-prefix')) ? $this->delimite($prefix) . '.' : '';
							$where .= $this->delimite($root->getAttribute('column'));
							break;
						case 'parameter':
							$parameterList[$id = ('p_' . sha1(random_bytes(64)))] = $root->getAttribute('parameter');
							$where .= ':' . $id;
							break;
						default:
							throw new QueryBuilderException(sprintf('Unknown where target type [%s] in [%s].', $target, static::class));
					}
					return (new NativeTransaction())->query(new NativeQuery($where, $parameterList));
				}
				switch ($type) {
					case 'group':
						return (new NativeTransaction())->query(new NativeQuery("(\n" . ($query = $this->fragmentWhereList($root))->getQuery() . "\t)", $query->getParameterList()));
					case 'in':
						switch ($target = $root->getAttribute('target')) {
							case 'query':
								return (new NativeTransaction())->query(new NativeQuery($this->delimite($root->getAttribute('where')) . " IN (\n" . ($query = $this->fragmentSelect($root->getNode('select')))->getQuery() . ')', $query->getParameterList()));
						}
						throw new QueryBuilderException(sprintf('Unknown where IN target type [%s].', $target));
				}
				throw new QueryBuilderException(sprintf('Unknown where type [%s].', $type));
			}

			protected function fragmentOrderList(INode $root): INativeTransaction {
				$orderList = [];
				foreach ($root->getNodeList() as $node) {
					$orderList[] = $this->delimite($node->getAttribute('column')) . ' ' . ($node->getAttribute('asc', true) ? 'ASC' : 'DESC');
				}
				return (new NativeTransaction())->query(new NativeQuery(implode(',', $orderList)));
			}
		}
