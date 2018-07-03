<?php
	declare(strict_types=1);

	namespace Edde\Common\Query;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\INodeQuery;
	use Edde\Api\Query\Exception\StaticQueryException;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Query\IStaticQuery;
	use Edde\Api\Query\IStaticQueryFactory;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Node\NodeQuery;
	use Edde\Common\Object\Object;
	use Edde\Common\Strings\StringUtils;
	use ReflectionClass;
	use ReflectionMethod;

	/**
	 * Helper class for IQL to string building.
	 */
	abstract class AbstractStaticQueryFactory extends Object implements IStaticQueryFactory {
		use ConfigurableTrait;
		/**
		 * @var array
		 */
		protected $factoryList = [];
		/**
		 * @var INodeQuery
		 */
		protected $selectNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $fromNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $whereNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $orderNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $createSchemaNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $updateQueryNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $updateQueryWhereNodeQuery;

		/**
		 * @inheritdoc
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		public function create(IQuery $query) {
			return $this->fragment($query->getNode());
		}

		/**
		 * @inheritdoc
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		public function fragment(INode $node) {
			if (isset($this->factoryList[$node->getName()]) === false) {
				throw new \Edde\Api\Query\Exception\StaticQueryException(sprintf('Unsuported fragment type [%s].', $node->getName()));
			}
			return $this->factoryList[$node->getName()]($node);
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 */
		protected function formatDeleteQuery(INode $node) {
			$sql = 'DELETE FROM ' . $this->delimite($node->getValue());
			return new StaticQuery($sql, []);
		}

		/**
		 * @param string $delimite
		 *
		 * @return string
		 */
		abstract protected function delimite(string $delimite): string;

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 */
		protected function formatInsertQuery(INode $node) {
			$parameterList = [];
			$nameList = [];
			$columnList = [];
			foreach ($node->getNodeList() as $insertNode) {
				$parameterList[$parameter = sha1($name = $insertNode->getName())] = $insertNode->getValue();
				$nameList[] = $this->delimite($name);
				$columnList[] = ':' . $parameter;
			}
			$sql = 'INSERT INTO ' . $this->delimite($node->getValue()) . ' (';
			$sql .= implode(',', $nameList) . ') VALUES (';
			return new StaticQuery($sql . implode(', ', $columnList) . ')', $parameterList);
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		protected function formatUpdateQuery(INode $node) {
			$parameterList = [];
			$updateQuery[] = 'UPDATE ' . $this->delimite($node->getValue()) . ' SET';
			$updateList = [];
			foreach ($this->updateQueryNodeQuery->filter($node) as $updateNode) {
				$parameterList[$parameter = sha1($name = $updateNode->getName())] = $updateNode->getValue();
				$updateList[] = $this->delimite($name) . ' = :' . $parameter;
			}
			$updateQuery[] = implode(', ', $updateList);
			if ($this->updateQueryWhereNodeQuery->isEmpty($node) === false) {
				$updateQuery[] = 'WHERE';
				$where = $this->formatWhere($node, $this->updateQueryWhereNodeQuery);
				$updateQuery[] = $where->getQuery();
				$parameterList = array_merge($parameterList, $where->getParameterList());
			}
			return new StaticQuery(implode(' ', $updateQuery), $parameterList);
		}

		/**
		 * @param INode      $node
		 * @param INodeQuery $nodeQuery
		 *
		 * @return IStaticQuery
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		protected function formatWhere(INode $node, INodeQuery $nodeQuery = null) {
			$nodeQuery = $nodeQuery ?: $this->whereNodeQuery;
			return $this->formatWhereList($nodeQuery->filter($node));
		}

		/**
		 * @param \Iterator|\Traversable|array $iterator
		 * @param bool                         $group
		 *
		 * @return StaticQuery
		 * @throws StaticQueryException
		 */
		protected function formatWhereList($iterator, $group = false) {
			$whereList = [];
			$parameterList = [];
			/** @var $whereNode INode */
			foreach ($iterator as $whereNode) {
				$staticQuery = $this->fragment($whereNode);
				$whereList[] = ' ' . strtoupper($whereNode->getAttribute('relation', 'and')) . ' ';
				$whereList[] = $staticQuery->getQuery();
				/** @noinspection SlowArrayOperationsInLoopInspection */
				$parameterList = array_merge($parameterList, $staticQuery->getParameterList());
			}
			/**
			 * throw away first member of the array which is dummy relation
			 */
			array_shift($whereList);
			$where = implode('', $whereList);
			if ($group) {
				$where = "($where)";
			}
			return new StaticQuery($where, $parameterList);
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 */
		protected function formatCreateSchemaQuery(INode $node) {
			$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->delimite($node->getValue()) . ' (';
			$columnList = [];
			foreach ($this->createSchemaNodeQuery->filter($node) as $propertyNode) {
				$column = $this->delimite($propertyNode->getName()) . ' ' . $this->type($propertyNode->getAttribute('type'));
				if ($propertyNode->getAttribute('identifier', false)) {
					$column .= ' PRIMARY KEY';
				} else if ($propertyNode->getAttribute('unique', false)) {
					$column .= ' UNIQUE';
				}
				if ($propertyNode->getAttribute('required', true)) {
					$column .= ' NOT NULL';
				}
				$columnList[] = $column;
			}
			return new StaticQuery($sql . implode(',', $columnList) . ')', []);
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		protected function formatSelectQuery(INode $node) {
			$selectList = $this->formatSelect($node);
			$parameterList = [];
			$selectQuery[] = 'SELECT';
			$selectQuery[] = $selectList->getQuery();
			$parameterList = array_merge($parameterList, $selectList->getParameterList());
			if ($this->fromNodeQuery->isEmpty($node) === false) {
				$selectQuery[] = 'FROM';
				$from = $this->formatFrom($node);
				$selectQuery[] = $from->getQuery();
				$parameterList = array_merge($parameterList, $from->getParameterList());
			}
			if ($this->whereNodeQuery->isEmpty($node) === false) {
				$selectQuery[] = 'WHERE';
				$where = $this->formatWhere($node);
				$selectQuery[] = $where->getQuery();
				$parameterList = array_merge($parameterList, $where->getParameterList());
			}
			if ($this->orderNodeQuery->isEmpty($node) === false) {
				$selectQuery[] = 'ORDER BY';
				$order = $this->formatOrder($node);
				$selectQuery[] = $order->getQuery();
				$parameterList = array_merge($parameterList, $order->getParameterList());
			}
			return new StaticQuery(implode(' ', $selectQuery), $parameterList);
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		protected function formatSelect(INode $node) {
			$parameterList = [];
			$selectList = [];
			foreach ($this->selectNodeQuery->filter($node) as $selectNode) {
				$staticQuery = $this->fragment($selectNode);
				$selectList[] = $staticQuery->getQuery();
				/** @noinspection SlowArrayOperationsInLoopInspection */
				$parameterList = array_merge($parameterList, $staticQuery->getParameterList());
			}
			return new StaticQuery(implode(', ', $selectList), $parameterList);
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws StaticQueryException
		 */
		protected function formatFrom(INode $node) {
			$parameterList = [];
			$fromList = [];
			foreach ($this->fromNodeQuery->filter($node) as $fromNode) {
				$staticQuery = $this->fragment($fromNode);
				$fromList[] = $staticQuery->getQuery();
				/** @noinspection SlowArrayOperationsInLoopInspection */
				$parameterList = array_merge($parameterList, $staticQuery->getParameterList());
			}
			return new StaticQuery(implode(', ', $fromList), $parameterList);
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		protected function formatOrder(INode $node) {
			$parameterList = [];
			$orderList = [];
			foreach ($this->orderNodeQuery->filter($node) as $orderNode) {
				$staticQuery = $this->fragment($orderNode);
				$orderList[] = $staticQuery->getQuery() . ' ' . (in_array($order = $orderNode->getAttribute('order'), [
						'asc',
						'desc',
					], true) ? strtoupper($order) : 'ASC');
				/** @noinspection SlowArrayOperationsInLoopInspection */
				$parameterList = array_merge($parameterList, $staticQuery->getParameterList());
			}
			return new StaticQuery(implode(', ', $orderList), $parameterList);
		}

		/**
		 * @return StaticQuery
		 */
		protected function formatAll() {
			return new StaticQuery('*');
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 */
		protected function formatProperty(INode $node) {
			$property = $this->delimite($node->getValue());
			if (($prefix = $node->getAttribute('prefix')) !== null) {
				$property = $this->delimite($prefix) . '.' . $property;
			}
			if (($alias = $node->getAttribute('alias')) !== null) {
				$property .= ' AS ' . $this->delimite($alias);
			}
			return new StaticQuery($property, []);
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 */
		protected function formatCount(INode $node) {
			$property = $this->delimite($node->getValue());
			if (($prefix = $node->getAttribute('prefix')) !== null) {
				$property = $this->delimite($prefix) . '.' . $property;
			}
			$property = 'COUNT(' . $property . ')';
			if (($alias = $node->getAttribute('alias')) !== null) {
				$property .= ' AS ' . $this->delimite($alias);
			}
			return new StaticQuery($property, []);
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 */
		protected function formatSource(INode $node) {
			$sql = $this->delimite($node->getValue());
			if (($alias = $node->getAttribute('alias')) !== null) {
				$sql .= ' ' . $this->delimite($alias);
			}
			return new StaticQuery($sql);
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		protected function formatWhereGroup(INode $node) {
			return $this->formatWhereList($node->getNodeList(), true);
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		protected function formatEqual(INode $node) {
			return $this->generateOperator($node, '=');
		}

		/**
		 * @param INode  $node
		 * @param string $operator
		 *
		 * @return StaticQuery
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		protected function generateOperator(INode $node, $operator) {
			if ($node->getNodeCount() !== 2) {
				throw new StaticQueryException(sprintf('Operator [%s] must have exactly two children.', $operator));
			}
			$alpha = $this->fragment($node->getNodeList()[0]);
			$beta = $this->fragment($node->getNodeList()[1]);
			return new StaticQuery($alpha->getQuery() . ' ' . $operator . ' ' . $beta->getQuery(), array_merge($alpha->getParameterList(), $beta->getParameterList()));
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws StaticQueryException
		 */
		protected function formatLike(INode $node) {
			return $this->generateOperator($node, 'LIKE');
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		protected function formatNotEqual(INode $node) {
			return $this->generateOperator($node, '!=');
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		protected function formatGreaterThan(INode $node) {
			return $this->generateOperator($node, '>');
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		protected function formatGreaterThanEqual(INode $node) {
			return $this->generateOperator($node, '>=');
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws StaticQueryException
		 */
		protected function formatLesserThan(INode $node) {
			return $this->generateOperator($node, '<');
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		protected function formatLesserThanEqual(INode $node) {
			return $this->generateOperator($node, '<=');
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		protected function formatIsNull(INode $node) {
			if ($node->getNodeCount() !== 1) {
				throw new \Edde\Api\Query\Exception\StaticQueryException('Is Null must have exactly one child.');
			}
			$alpha = $this->fragment($node->getNodeList()[0]);
			return new StaticQuery($alpha->getQuery() . ' IS NULL', $alpha->getParameterList());
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 * @throws \Edde\Api\Query\Exception\StaticQueryException
		 */
		protected function formatIsNotNull(INode $node) {
			if ($node->getNodeCount() !== 1) {
				throw new \Edde\Api\Query\Exception\StaticQueryException('Is Not Null must have exactly one child.');
			}
			$alpha = $this->fragment($node->getNodeList()[0]);
			return new StaticQuery($alpha->getQuery() . ' IS NOT NULL', $alpha->getParameterList());
		}

		/**
		 * @param INode $node
		 *
		 * @return StaticQuery
		 */
		protected function formatParameter(INode $node) {
			return new StaticQuery(':' . $hash = $node->getAttribute('name', hash('sha256', spl_object_hash($node))), [
				$hash => $node->getValue(),
			]);
		}

		/**
		 * @inheritdoc
		 */
		protected function handleInit() {
			$reflectionClass = new ReflectionClass($this);
			foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PROTECTED) as $reflectionMethod) {
				if (strpos($reflectionMethod->getName(), 'format') === false) {
					continue;
				}
				$name = StringUtils::recamel(str_replace('format', null, $reflectionMethod->getName()));
				$this->factoryList[$name] = [
					$this,
					$reflectionMethod->getName(),
				];
			}
			$this->selectNodeQuery = new NodeQuery('/select-query/select/*');
			$this->fromNodeQuery = new NodeQuery('/select-query/from/*');
			$this->whereNodeQuery = new NodeQuery('/select-query/where/*');
			$this->orderNodeQuery = new NodeQuery('/select-query/order/*');

			$this->createSchemaNodeQuery = new NodeQuery('/create-schema-query/*');
			$this->updateQueryNodeQuery = new NodeQuery('/update-query/update/*');
			$this->updateQueryWhereNodeQuery = new NodeQuery('/update-query/where/*');
		}

		/**
		 * @param string $quote
		 *
		 * @return string
		 */
		abstract protected function quote(string $quote): string;

		/**
		 * @param string $type
		 *
		 * @return string
		 */
		abstract protected function type(string $type): string;
	}
