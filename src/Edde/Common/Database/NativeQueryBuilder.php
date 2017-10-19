<?php
	namespace Edde\Common\Database;

		use Edde\Api\Database\Exception\DriverQueryException;
		use Edde\Api\Node\INode;
		use Edde\Api\Node\INodeQuery;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Utils\Inject\StringUtils;
		use Edde\Common\Node\NodeQuery;
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
			 * @var INodeQuery[]
			 */
			protected $nodeQueryList = [];

			/**
			 * @param INode $node
			 *
			 * @return INativeQuery
			 *
			 * @throws DriverQueryException
			 */
			public function fragment(INode $node): INativeQuery {
				if (isset($this->fragmentList[$name = $node->getName()]) === false) {
					throw new DriverQueryException(sprintf('Unsupported fragment type [%s].', $name));
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
				$this->nodeQueryList = [
					'select'        => new NodeQuery('/select/select/*'),
					'from'          => new NodeQuery('/select/from/*'),
					'where'         => new NodeQuery('/select/where/*'),
					'order'         => new NodeQuery('/select/order/*'),
					'create-schema' => new NodeQuery('/create-schema/*'),
					'update'        => new NodeQuery('/update/update/*'),
					'update-where'  => new NodeQuery('/update/where/*'),
				];
			}

			protected function fragmentCreateSchema(INode $node) {
				$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->delimite($node->getValue()) . ' (';
				$columnList = [];
				foreach ($this->nodeQueryList['create-schema']->filter($node) as $node) {
					$column = $this->delimite($node->getName()) . ' ' . $this->type($node->getAttribute('type'));
					if ($node->getAttribute('identifier', false)) {
						$column .= ' PRIMARY KEY';
					} else if ($node->getAttribute('unique', false)) {
						$column .= ' UNIQUE';
					}
					if ($node->getAttribute('required', true)) {
						$column .= ' NOT NULL';
					}
					$columnList[] = $column;
				}
				return new NativeQuery($sql . implode(',', $columnList) . ')');
			}
		}
