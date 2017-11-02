<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver\Database;

		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\Fragment\IWhereTo;
		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Api\Query\IInsertQuery;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Query\ITransactionQuery;
		use Edde\Api\Query\IUpdateQuery;
		use Edde\Common\Query\AbstractQueryBuilder;
		use Edde\Common\Query\TransactionQuery;

		abstract class AbstractSqlBuilder extends AbstractQueryBuilder {
			protected function fragmentCreateSchema(ICrateSchemaQuery $query) : INativeTransaction {
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
				return new TransactionQuery($sql . implode(",\n\t", $columnList) . "\n)");
			}

			protected function fragmentInsert(IInsertQuery $insertQuery) : INativeTransaction {
				$nameList = [];
				$parameterList = [];
				foreach ($insertQuery->getSource() as $k => $v) {
					$nameList[] = $this->delimite($k);
					$parameterList['p_' . sha1($k)] = $v;
				}
				$schema = $insertQuery->getSchema();
				$sql = 'INSERT INTO ' . $this->delimite($schema->getName()) . ' (' . implode(',', $nameList) . ') VALUES (';
				return new TransactionQuery($sql . ':' . implode(', :', array_keys($parameterList)) . ')', $parameterList);
			}

			/**
			 * @param ISelectQuery $selectQuery
			 *
			 * @return ITransactionQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentSelect(ISelectQuery $selectQuery) : ITransactionQuery {
				$columnList = [];
				$fromList = [];
				$whereList = null;
				$parameterList = [];
				foreach ($selectQuery->getSchemaFragmentList() as $schemaFragment) {
					$alias = $this->delimite($schemaFragment->getAlias());
					if ($schemaFragment->isSelected()) {
						$columnList[$alias] = $alias . '.*';
					}
					$fromList[$alias] = $this->delimite($schemaFragment->getSchema()->getName()) . ' ' . $alias;
					if ($schemaFragment->hasWhere()) {
						$whereList[] = ($query = $this->fragmentWhereGroup($schemaFragment->where()))->getQuery();
						$parameterList = array_merge($parameterList, $query->getParameterList());
					}
				}
				$sql = "SELECT\n\t" . implode(",\n\t", $columnList) . "\nFROM\n\t" . implode(",\n\t", $fromList) . "\n";
				if ($whereList) {
					$sql .= 'WHERE' . implode("AND\n", $whereList);
				}
				return new TransactionQuery($sql, $parameterList);
			}

			/**
			 * @param IUpdateQuery $updateQuery
			 *
			 * @return INativeTransaction
			 * @throws QueryBuilderException
			 */
			protected function fragmentUpdate(IUpdateQuery $updateQuery) : INativeTransaction {
				$schema = $updateQuery->getSchema();
				$schemaFragment = $updateQuery->getSchemaFragment();
				$sql = "UPDATE\n\t";
				$sql .= $this->delimite($schema->getName()) . ' ' . $this->delimite($schemaFragment->getAlias()) . "\n";
				$sql .= "SET\n\t";
				$parameterList = [];
				$nameList = [];
				foreach ($updateQuery->getSource() as $k => $v) {
					$nameList[] = $this->delimite($k) . ' = :' . ($parameterId = ('p_' . sha1($k)));
					$parameterList[$parameterId] = $v;
				}
				$sql .= implode(",\n\t", $nameList) . "\n";
				if ($updateQuery->hasWhere()) {
					$sql .= 'WHERE' . ($query = $this->fragmentWhereGroup($updateQuery->where()))->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				return new TransactionQuery($sql, $parameterList);
			}

			/**
			 * @param IWhereGroup $whereGroup
			 *
			 * @return ITransactionQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentWhereGroup(IWhereGroup $whereGroup) : ITransactionQuery {
				$whereList = null;
				$parameterList = [];
				foreach ($whereGroup as $where) {
					$sql = "\n\t";
					if ($whereList) {
						$sql = ' ' . strtoupper($where->getRelation()) . "\n\t";
					}
					$whereList .= $sql . ($query = $this->fragmentWhere($where))->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				return new TransactionQuery($whereList, $parameterList);
			}

			/**
			 * @param IWhere $where
			 *
			 * @return ITransactionQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentWhere(IWhere $where) : ITransactionQuery {
				return $this->fragment($where->getExpression());
			}

			/**
			 * @param IWhereTo $whereTo
			 *
			 * @return INativeTransaction
			 * @throws QueryBuilderException
			 * @throws \Exception
			 */
			protected function fragmentWhereExpressionEq(IWhereTo $whereTo) : ITransactionQuery {
				$name = $this->delimite($whereTo->getSchemaFragment()->getAlias()) . '.' . $this->delimite($whereTo->getName());
				switch ($target = $whereTo->getTarget()) {
					case 'column':
						list($prefix, $column) = $whereTo->getValue();
						return new TransactionQuery($name . ' = ' . $this->delimite($prefix) . '.' . $this->delimite($column));
					case 'value':
						return new TransactionQuery($name . ' = :' . ($parameterId = 'p_' . sha1($target . microtime(true) . random_bytes(8))), [
							$parameterId => $whereTo->getValue(),
						]);
				}
				throw new QueryBuilderException(sprintf('Unknown where expression [%s] target [%s].', $whereTo->getType(), $target));
			}
		}
