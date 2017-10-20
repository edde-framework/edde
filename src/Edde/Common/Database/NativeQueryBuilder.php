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
							if ($node->getAttribute('all')) {
								$selectList[] = ($alias ? $alias . '.' : '') . '*';
							}
							foreach ($node->getNodeList() as $column) {
								$query = $this->fragment($column);
								$selectList[] = $query->getQuery();
								$parameterList = array_merge($parameterList, $query->getParameterList());
							}
							break;
						case 'parameter':
							$parameterList[$node->getAttribute('name')] = $node->getValue();
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
			protected function fragmentColumn(INode $root): INativeQuery {
				switch ($type = $root->getAttribute('type')) {
					case 'column':
						$table = $root->getParent();
						$column = ($alias = $table->getAttribute('alias')) ? $this->delimite($alias) . '.' : '';
						$column .= $this->delimite($root->getValue());
						$column .= ($alias = $root->getAttribute('alias')) ? ' AS ' . $this->delimite($alias) : '';
						return new NativeQuery($column);
				}
				throw new DriverQueryException(sprintf('Unknown column type [%s].', $type));
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
