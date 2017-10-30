<?php
	namespace Edde\Ext\Driver\Database;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\INativeBatch;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Query\AbstractQueryBuilder;
		use Edde\Common\Query\NativeBatch;

		abstract class AbstractSqlBuilder extends AbstractQueryBuilder {
			protected function fragmentCreateSchema(INode $root): INativeBatch {
				$sql = 'CREATE TABLE ' . ($this->delimite($table = $root->getAttribute('name'))) . " (\n\t";
				$columnList = [];
				$primaryList = [];
				foreach ($root->getNodeList() as $node) {
					$column = ($name = $this->delimite($node->getAttribute('name'))) . ' ' . $this->type($node->getAttribute('type'));
					if ($node->getAttribute('primary', false)) {
						$primaryList[] = $name;
					} else if ($node->getAttribute('unique', false)) {
						$column .= ' UNIQUE';
					}
					if ($node->getAttribute('required', false)) {
						$column .= ' NOT NULL';
					}
					$columnList[] = $column;
				}
				if (empty($primaryList) === false) {
					$columnList[] = "CONSTRAINT " . $this->delimite(sha1($table . '_primary_' . $primary = implode(', ', $primaryList))) . ' PRIMARY KEY (' . $primary . ")\n";
				}
				$sql .= implode(",\n\t", $columnList) . "\n";
				return new NativeBatch($sql . ')');
			}

			protected function fragmentRelation(INode $root): INativeBatch {
				return $this->fragmentInsert($root);
			}

			protected function fragmentInsert(INode $root): INativeBatch {
				$nameList = [];
				$parameterList = [];
				foreach ($root->getValue([]) as $k => $v) {
					$nameList[] = $this->delimite($k);
					$parameterList['p_' . sha1($k)] = $v;
				}
				/** @var $schema ISchema */
				$schema = $root->getAttribute('schema');
				$sql = 'INSERT INTO ' . $this->delimite($schema->getName()) . ' (';
				$sql .= implode(',', $nameList) . ') VALUES (';
				return new NativeBatch($sql . ':' . implode(', :', array_keys($parameterList)) . ')', $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeBatch
			 * @throws QueryBuilderException
			 */
			protected function fragmentUpdate(INode $root): INativeBatch {
				/** @var $schema ISchema */
				$schema = $root->getAttribute('schema');
				$sql = "UPDATE\n\t" . $this->delimite($schema->getName()) . ' ' . ($alias = ($alias = $root->getAttribute('alias')) ? $this->delimite($alias) : '') . "\nSET\n\t";
				$where = null;
				$setList = [];
				$parameterList = [];
				/**
				 * micro-optimization to eliminate array_merge over parameter list
				 */
				if ($root->hasNode('where-list')) {
					$query = $this->fragmentWhereList($root->getNode('where-list'));
					$where .= "WHERE\n" . $query->getQuery();
					$parameterList = $query->getParameterList();
				}
				foreach ($root->getValue([]) as $k => $v) {
					$setList[] = $this->delimite($k) . ' = :' . $parameterId = ('p_' . sha1($k));
					$parameterList[$parameterId] = $v;
				}
				return new NativeBatch($sql . implode(",\n\t", $setList) . "\n" . $where, $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeBatch
			 * @throws QueryBuilderException
			 */
			protected function fragmentSelect(INode $root): INativeBatch {
				$sql = [];
				$parameterList = [];
				$sql[] = "SELECT\n";
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
				return new NativeBatch(implode('', $sql), $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeBatch
			 * @throws QueryBuilderException
			 */
			protected function fragmentColumnList(INode $root): INativeBatch {
				$columnList = [];
				$parameterList = [];
				foreach ($root->getNodeList() as $node) {
					$query = $this->fragment($node);
					$columnList[] = "\t" . $query->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				return new NativeBatch(implode(",\n", $columnList) . "\n", $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeBatch
			 * @throws QueryBuilderException
			 */
			protected function fragmentColumn(INode $root): INativeBatch {
				switch ($type = $root->getAttribute('type')) {
					case 'column':
						$column = ($prefix = $root->getAttribute('prefix')) ? $this->delimite($prefix) . '.' : '';
						$column .= $this->delimite($root->getValue());
						$column .= ($alis = $root->getAttribute('alias')) ? ' AS ' . $this->delimite($alis) : '';
						return new NativeBatch($column);
					case 'asterisk':
						$column = ($prefix = $root->getAttribute('prefix')) ? $this->delimite($prefix) . '.' : '';
						$column .= '*';
						return new NativeBatch($column);
				}
				throw new QueryBuilderException(sprintf('Unknown column type [%s].', $type));
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeBatch
			 * @throws QueryBuilderException
			 */
			protected function fragmentTableList(INode $root): INativeBatch {
				$tableList = [];
				$parameterList = [];
				foreach ($root->getNodeList() as $node) {
					$query = $this->fragment($node);
					$tableList[] = "\t" . $query->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				return new NativeBatch(implode(",\n", $tableList) . "\n", $parameterList);
			}

			protected function fragmentTable(INode $root): INativeBatch {
				$table = $this->delimite($root->getAttribute('table'));
				$table .= (($alias = $root->getAttribute('alias')) ? ' ' . $this->delimite($alias) : '');
				return new NativeBatch($table);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeBatch
			 * @throws QueryBuilderException
			 * @throws \Exception
			 */
			protected function fragmentWhere(INode $root): INativeBatch {
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
					return new NativeBatch($where, $parameterList);
				}
				switch ($type) {
					case 'group':
						return new NativeBatch("(\n" . ($query = $this->fragmentWhereList($root))->getQuery() . "\t)", $query->getParameterList());
					case 'in':
						switch ($target = $root->getAttribute('target')) {
							case 'query':
								return new NativeBatch($this->delimite($root->getAttribute('where')) . " IN (\n" . ($query = $this->fragmentSelect($root->getNode('select')))->getQuery() . ')', $query->getParameterList());
						}
						throw new QueryBuilderException(sprintf('Unknown where IN target type [%s].', $target));
				}
				throw new QueryBuilderException(sprintf('Unknown where type [%s].', $type));
			}

			protected function fragmentOrderList(INode $root): INativeBatch {
				$orderList = [];
				foreach ($root->getNodeList() as $node) {
					$orderList[] = $this->delimite($node->getAttribute('column')) . ' ' . ($node->getAttribute('asc', true) ? 'ASC' : 'DESC');
				}
				return new NativeBatch(implode(',', $orderList));
			}
		}
