<?php
	namespace Edde\Ext\Driver\Database;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Query\IQueryBuilder;
		use Edde\Api\Utils\Inject\StringUtils;
		use Edde\Common\Object\Object;
		use Edde\Common\Query\NativeQuery;
		use ReflectionClass;
		use ReflectionMethod;

		abstract class AbstractQueryBuilder extends Object implements IQueryBuilder {
			use StringUtils;
			protected $fragmentList = [];

			/**
			 * @param IQuery $query
			 *
			 * @return INativeQuery
			 * @throws QueryBuilderException
			 */
			public function build(IQuery $query): INativeQuery {
				return $this->fragment($query->getQuery());
			}

			/**
			 * @param INode $node
			 *
			 * @return INativeQuery
			 *
			 * @throws QueryBuilderException
			 */
			public function fragment(INode $node): INativeQuery {
				if (isset($this->fragmentList[$name = $node->getName()]) === false) {
					throw new QueryBuilderException(sprintf('Unsupported fragment type [%s] in [%s].', $name, static::class));
				}
				return $this->fragmentList[$name]($node);
			}

			protected function initNativeQueryBuilder() {
				$reflectionClass = new ReflectionClass($this);
				foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PROTECTED) as $reflectionMethod) {
					if (strpos($name = $reflectionMethod->getName(), 'fragment') === false || strlen($name) <= 8) {
						continue;
					}
					$this->fragmentList[$this->stringUtils->recamel(substr($name, 8))] = [
						$this,
						$name,
					];
				}
			}

			protected function fragmentCreateSchema(INode $root): INativeQuery {
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
					$columnList[] = "CONSTRAINT " . $this->delimite($table . '_primary_' . sha1($primary = implode(', ', $primaryList))) . ' PRIMARY KEY (' . $primary . ")\n";
				}
				$sql .= implode(",\n\t", $columnList) . "\n";
				return new NativeQuery($sql . ')');
			}

			protected function fragmentInsert(INode $root): INativeQuery {
				$parameterList = $this->fragmentParameterList($root->getNode('parameter-list'))->getParameterList();
				$nameList = [];
				$columnList = [];
				foreach ($root->getNode('column-list')->getNodeList() as $node) {
					$nameList[] = $this->delimite($node->getAttribute('column'));
					$columnList[] = ':' . $node->getAttribute('parameter');
				}
				$sql = 'INSERT INTO ' . $this->delimite($root->getAttribute('table')) . ' (';
				$sql .= implode(',', $nameList) . ') VALUES (';
				return new NativeQuery($sql . implode(', ', $columnList) . ')', $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentUpdate(INode $root): INativeQuery {
				$sql[] = "UPDATE\n\t" . $this->delimite($root->getAttribute('table')) . "\nSET\n\t";
				$parameterList = $this->fragment($root->getNode('parameter-list'))->getParameterList();
				$setList = [];
				foreach ($root->getNode('column-list')->getNodeList() as $node) {
					$setList[] = $this->delimite($node->getAttribute('column')) . ' = :' . $node->getAttribute('parameter');
				}
				$sql[] = implode(",\n\t", $setList) . "\n";
				if ($root->hasNode('where-list')) {
					$sql[] = "WHERE\n";
					$query = $this->fragmentWhereList($root->getNode('where-list'));
					$sql[] = $query->getQuery();
					array_merge($parameterList, $query->getParameterList());
				}
				return new NativeQuery(implode('', $sql), $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return NativeQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentSelect(INode $root) {
				$sql = [];
				$parameterList = [];
				$sql[] = "SELECT\n";
				$query = $this->fragmentColumnList($root->getNode('column-list'));
				$sql[] = $query->getQuery();
				array_merge($parameterList, $query->getParameterList());
				if ($root->hasNode('table-list')) {
					$sql[] = "FROM\n";
					$query = $this->fragmentTableList($root->getNode('table-list'));
					$sql[] = $query->getQuery();
					array_merge($parameterList, $query->getParameterList());
				}
				if ($root->hasNode('where-list')) {
					$sql[] = "WHERE\n";
					$query = $this->fragmentWhereList($root->getNode('where-list'));
					$sql[] = $query->getQuery();
					array_merge($parameterList, $query->getParameterList());
				}
				if ($root->hasNode('order-list')) {
					$sql[] = "ORDER BY\n";
					$query = $this->fragmentOrderList($root->getNode('order-list'));
					$sql[] = $query->getQuery();
					array_merge($parameterList, $query->getParameterList());
				}
				$parameterList = array_merge($parameterList, ($this->fragmentParameterList($root->getNode('parameter-list')))->getParameterList());
				$sql = implode('', $sql);
				return new NativeQuery($sql, $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentColumnList(INode $root): INativeQuery {
				$columnList = [];
				$parameterList = [];
				foreach ($root->getNodeList() as $node) {
					$query = $this->fragment($node);
					$columnList[] = "\t" . $query->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				return new NativeQuery(implode(",\n", $columnList) . "\n", $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentColumn(INode $root): INativeQuery {
				switch ($type = $root->getAttribute('type')) {
					case 'column':
						$column = ($prefix = $root->getAttribute('prefix')) ? $this->delimite($prefix) . '.' : '';
						$column .= $this->delimite($root->getValue());
						$column .= ($alis = $root->getAttribute('alias')) ? ' AS ' . $this->delimite($alis) : '';
						return new NativeQuery($column);
					case 'asterisk':
						$column = ($prefix = $root->getAttribute('prefix')) ? $this->delimite($prefix) . '.' : '';
						$column .= '*';
						return new NativeQuery($column);
				}
				throw new QueryBuilderException(sprintf('Unknown column type [%s].', $type));
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentTableList(INode $root): INativeQuery {
				$tableList = [];
				$parameterList = [];
				foreach ($root->getNodeList() as $node) {
					$query = $this->fragment($node);
					$tableList[] = "\t" . $query->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				return new NativeQuery(implode(",\n", $tableList) . "\n", $parameterList);
			}

			protected function fragmentTable(INode $root): INativeQuery {
				$table = $this->delimite($root->getValue());
				$table .= (($alias = $root->getAttribute('alias')) ? ' ' . $this->delimite($alias) : '');
				return new NativeQuery($table);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentWhereList(INode $root): INativeQuery {
				$whereList = null;
				$parameterList = [];
				foreach ($root->getNodeList() as $node) {
					$query = $this->fragment($node);
					$where = null;
					if ($whereList && ($relationTo = $node->getAttribute('relation-to'))) {
						$where .= ' ' . strtoupper($relationTo);
					}
					$where .= $query->getQuery();
					$whereList[] = $where . ' ' . strtoupper($node->getAttribute('relation'));
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				return new NativeQuery("\t" . implode("\n\t", $whereList), $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentWhere(INode $root): INativeQuery {
				$where = null;
				static $expressions = [
					'eq'  => '=',
					'neq' => '!=',
					'gt'  => '>',
					'gte' => '>=',
					'lt'  => '<',
					'lte' => '<=',
				];
				if (isset($expressions[$type = $root->getAttribute('type')])) {
					$where = $this->delimite($root->getAttribute('where')) . ' ' . $expressions[$type] . ' ';
					switch ($root->getAttribute('target', 'column')) {
						case 'column':
							$where .= $this->delimite($root->getAttribute('column'));
							break;
						case 'parameter':
							$where .= ':' . $root->getAttribute('parameter');
							break;
					}
					return new NativeQuery($where);
				}
				switch ($type) {
					case 'group':
						return new NativeQuery("(\n" . ($query = $this->fragmentWhereList($root))->getQuery() . "\t)", $query->getParameterList());
					case 'in':
						switch ($target = $root->getAttribute('target')) {
							case 'query':
								return new NativeQuery($this->delimite($root->getAttribute('where')) . " IN (\n" . ($query = $this->fragmentSelect($root->getNode('select')))->getQuery() . ')', $query->getParameterList());
						}
						throw new QueryBuilderException(sprintf('Unknown where IN target type [%s].', $target));
				}
				throw new QueryBuilderException(sprintf('Unknown where type [%s].', $type));
			}

			protected function fragmentOrderList(INode $root): INativeQuery {
				$orderList = [];
				foreach ($root->getNodeList() as $node) {
					$orderList[] = $this->delimite($node->getAttribute('column')) . ' ' . ($node->getAttribute('asc', true) ? 'ASC' : 'DESC');
				}
				return new NativeQuery(implode(',', $orderList));
			}

			protected function fragmentParameterList(INode $root): INativeQuery {
				$parameterList = [];
				foreach ($root->getNodeList() as $node) {
					$parameterList[$node->getAttribute('name')] = $node->getValue();
				}
				return new NativeQuery('', $parameterList);
			}
		}
