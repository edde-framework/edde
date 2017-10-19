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

			protected function fragmentSelect(INode $root) {
				$tableList = null;
				$selectList = [];
				$parameterList = [];
				$sql[] = 'SELECT';
				foreach ($root->getNodeList() as $table) {
					switch ($table->getName()) {
						case 'table':
							$name = $this->delimite($table->getValue());
							if (($alias = $table->getAttribute('alias')) !== null) {
								$name .= ' AS ' . ($alias = $this->delimite($alias));
							}
							$tableList[] = $name;
							$prefix = ($alias ? $alias . '.' : '');
							if ($table->getAttribute('all')) {
								$selectList[] = $prefix . '*';
							}
							foreach ($table->getNodeList() as $column) {
								$selectList[] = $prefix . $this->delimite($column->getValue());
							}
							break;
					}
				}
				$sql[] = implode(', ', $selectList);
				if ($tableList) {
					$sql[] = 'FROM';
					$sql[] = implode(', ', $tableList);
				}
				return new NativeQuery(implode("\n", $sql), $parameterList);
			}
		}
