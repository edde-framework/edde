<?php
	namespace Edde\Common\Database;

		use Edde\Api\Database\Exception\DriverQueryException;
		use Edde\Api\Node\INode;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Utils\Inject\StringUtils;
		use Edde\Common\Query\NativeQuery;
		use ReflectionClass;
		use ReflectionMethod;

		/**
		 * Helper trait to share common SQL generation code between driverss.
		 */
		trait NativeQueryBuilder {
			use StringUtils;
			protected $fragmentList = [];

			/**
			 * @param INode $node
			 *
			 * @return INativeQuery
			 *
			 * @throws DriverQueryException
			 */
			public function fragment(INode $node): INativeQuery {
				if (isset($this->fragmentList[$name = $node->getName()]) === false) {
					throw new DriverQueryException(sprintf('Unsupported fragment type [%s] in [%s].', $name, static::class));
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

			protected function fragmentCreateSchema(INode $root) {
				$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->delimite($root->getValue()) . ' (';
				$columnList = [];
				foreach ($root->getNodeList() as $node) {
					$column = $this->delimite($node->getName()) . ' ' . $this->type($node->getAttribute('type'));
					if ($node->getAttribute('primary', false)) {
						$column .= ' PRIMARY KEY';
					} else if ($node->getAttribute('unique', false)) {
						$column .= ' UNIQUE';
					}
					if ($node->getAttribute('required', false)) {
						$column .= ' NOT NULL';
					}
					$columnList[] = $column;
				}
				return new NativeQuery($sql . implode(',', $columnList) . ')');
			}

			protected function fragmentInsert(INode $root) {
				$parameterList = [];
				$nameList = [];
				$columnList = [];
				foreach ($root->getAttributeList() as $k => $v) {
					$parameterList[$parameter = sha1($k)] = $v;
					$nameList[] = $this->delimite($k);
					$columnList[] = ':' . $parameter;
				}
				$sql = 'INSERT INTO ' . $this->delimite($root->getValue()) . ' (';
				$sql .= implode(',', $nameList) . ') VALUES (';
				return new NativeQuery($sql . implode(', ', $columnList) . ')', $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return NativeQuery
			 * @throws DriverQueryException
			 */
			protected function fragmentSelect(INode $root) {
				$sql = [];
				$parameterList = [];
				$sql[] = "SELECT\n";
				$query = $this->fragment($root->getNode('column-list'));
				$sql[] = $query->getQuery();
				array_merge($parameterList, $query->getParameterList());
				if ($root->hasNode('table-list')) {
					$sql[] = "FROM\n";
					$query = $this->fragment($root->getNode('table-list'));
					$sql[] = $query->getQuery();
					array_merge($parameterList, $query->getParameterList());
				}
				if ($root->hasNode('where-list')) {
					$sql[] = "WHERE\n";
					$query = $this->fragment($root->getNode('where-list'));
					$sql[] = $query->getQuery();
					array_merge($parameterList, $query->getParameterList());
				}
				array_merge($parameterList, ($this->fragment($root->getNode('parameter-list')))->getParameterList());
				$sql = implode('', $sql);
				return new NativeQuery($sql, $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeQuery
			 * @throws DriverQueryException
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
			 * @throws DriverQueryException
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
				throw new DriverQueryException(sprintf('Unknown column type [%s].', $type));
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeQuery
			 * @throws DriverQueryException
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
			 * @throws DriverQueryException
			 */
			protected function fragmentWhereList(INode $root): INativeQuery {
				$whereList = [];
				$parameterList = [];
				foreach ($root->getNodeList() as $node) {
					$query = $this->fragment($node);
					$where = null;
					if ($relationTo = $node->getAttribute('relation-to')) {
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
			 * @throws DriverQueryException
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
								return new NativeQuery($this->delimite($root->getAttribute('where')) . " IN (\n" . ($query = $this->fragment($root->getNode('select')))->getQuery() . ')', $query->getParameterList());
						}
						throw new DriverQueryException(sprintf('Unknown where IN target type [%s].', $target));
				}
				throw new DriverQueryException(sprintf('Unknown where type [%s].', $type));
			}

			protected function fragmentParameterList(INode $root): INativeQuery {
				$parameterList = [];
				if (($node = $root->getNode('parameter-list'))->isLeaf() === false) {
					foreach ($node->getNodeList() as $node) {
						$parameterList[$node->getAttribute('name')] = $node->getValue();
					}
				}
				return new NativeQuery('', $parameterList);
			}
		}
