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
				$tableList = null;
				$selectList = [];
				$whereList = null;
				$parameterList = [];
				$sql[] = 'SELECT';
				foreach ($root->getNodeList() as $node) {
					switch ($node->getName()) {
						case 'table':
							$name = $this->delimite($node->getValue());
							if (($alias = $node->getAttribute('alias')) !== null) {
								$name .= ' AS ' . ($alias = $this->delimite($alias));
							}
							$tableList[] = $name;
							$prefix = ($alias ? $alias . '.' : '');
							if ($node->getAttribute('all')) {
								$selectList[] = $prefix . '*';
							}
							foreach ($node->getNodeList() as $column) {
								$selectList[] = $prefix . $this->delimite($column->getValue());
							}
							break;
						case 'parameter':
							$parameterList[$node->getAttribute('name')] = $node->getValue();
							break;
						case 'where':
							if ($whereList && ($relationTo = $node->getAttribute('relation-to'))) {
								$whereList[] = strtoupper($relationTo);
							}
							$whereList[] = ($query = $this->fragmentWhere($node))->getQuery() . ' ';
							$parameterList = array_merge($parameterList, $query->getParameterList());
							$whereList[] = strtoupper($node->getAttribute('relation')) . "\n";
							break;
					}
				}
				$sql[] = implode(', ', $selectList);
				if ($tableList) {
					$sql[] = 'FROM';
					$sql[] = implode(', ', $tableList);
				}
				if ($whereList) {
					$sql[] = 'WHERE';
					$sql[] = implode('', $whereList);
				}
				return new NativeQuery(sprintf($root->isRoot() ? '%s' : ('(%s)' . (($alias = $root->getAttribute('alias')) ? ' AS ' . $this->delimite($alias) : '')), implode(' ', $sql)), $parameterList);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeQuery
			 * @throws DriverQueryException
			 */
			protected function fragmentWhere(INode $root): INativeQuery {
				$whereList = [];
				$parameterList = [];
				foreach ($root->getNodeList() as $node) {
					$whereList[] = ($query = $this->formatWhereExpression($node))->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
					$whereList[] = strtoupper($node->getAttribute('relation')) . "\n";
				}
				return new NativeQuery("(\n" . implode('', $whereList) . ')', $parameterList);
			}

			protected function formatWhereExpression(INode $root): INativeQuery {
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
			}
		}
