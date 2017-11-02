<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver\Database;

		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Query\Fragment\IWhereTo;
		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Api\Query\IInsertQuery;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Query\IUpdateQuery;
		use Edde\Common\Query\AbstractQueryBuilder;
		use Edde\Common\Query\NativeQuery;

		abstract class AbstractSqlBuilder extends AbstractQueryBuilder {
			protected function fragmentCreateSchema(ICrateSchemaQuery $query): INativeQuery {
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
				return new NativeQuery($sql . implode(",\n\t", $columnList) . "\n)");
			}

			protected function fragmentInsert(IInsertQuery $insertQuery): INativeQuery {
				$nameList = [];
				$parameterList = [];
				foreach ($insertQuery->getSource() as $k => $v) {
					$nameList[] = $this->delimite($k);
					$parameterList['p_' . sha1($k)] = $v;
				}
				$schema = $insertQuery->getSchema();
				$sql = 'INSERT INTO ' . $this->delimite($schema->getName()) . ' (' . implode(',', $nameList) . ') VALUES (';
				return new NativeQuery($sql . ':' . implode(', :', array_keys($parameterList)) . ')', $parameterList);
			}

			protected function fragmentSelect(ISelectQuery $selectQuery): INativeQuery {
				$columnList = [];
				$fromList = [];
				foreach ($selectQuery->getSchemaFragmentList() as $schemaFragment) {
					$columnList[$alias] = ($alias = $this->delimite($schemaFragment->getAlias())) . '.*';
					$fromList[$alias] = $this->delimite($schemaFragment->getSchema()->getName()) . ' ' . $alias;
				}
				$sql = "SELECT\n\t" . implode(",\n\t", $columnList) . "\nFROM\n\t" . implode(",\n\t", $fromList);
				return new NativeQuery($sql);
			}

			/**
			 * @param IUpdateQuery $updateQuery
			 *
			 * @return INativeQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentUpdate(IUpdateQuery $updateQuery): INativeQuery {
				$schema = $updateQuery->getSchema();
				$sql = "UPDATE\n\t";
				$sql .= $this->delimite($schema->getName()) . "\n";
				$sql .= "SET\n\t";
				$parameterList = [];
				$nameList = [];
				foreach ($updateQuery->getSource() as $k => $v) {
					$nameList[] = $this->delimite($k) . ' = :' . ($parameterId = ('p_' . sha1($k)));
					$parameterList[$parameterId] = $v;
				}
				$sql .= implode(",\n\t", $nameList) . "\n";
				if ($updateQuery->hasWhere()) {
					$sql .= "WHERE\n\t" . ($query = $this->fragmentWhereGroup($updateQuery->where()))->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				return new NativeQuery($sql, $parameterList);
			}

			/**
			 * @param IWhereGroup $whereGroup
			 *
			 * @return INativeQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentWhereGroup(IWhereGroup $whereGroup): INativeQuery {
				$whereList = [];
				$parameterList = [];
				foreach ($whereGroup as $where) {
					$whereList[] = strtoupper($where->getRelation());
					$whereList[] = ($query = $this->fragmentWhere($where))->getQuery();
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				array_shift($whereList);
				return new NativeQuery(implode(",\n\t", $whereList), $parameterList);
			}

			/**
			 * @param IWhere $where
			 *
			 * @return INativeQuery
			 * @throws QueryBuilderException
			 */
			protected function fragmentWhere(IWhere $where): INativeQuery {
				return $this->fragment($where->getExpression());
			}

			/**
			 * @param IWhereTo $whereTo
			 *
			 * @return INativeQuery
			 * @throws QueryBuilderException
			 * @throws \Exception
			 */
			protected function fragmentWhereExpressionEq(IWhereTo $whereTo): INativeQuery {
				switch ($target = $whereTo->getTarget()) {
					case 'column':
						return new NativeQuery($this->delimite($whereTo->getName()) . ' = ' . $this->delimite($whereTo->getValue()));
					case 'value':
						return new NativeQuery($this->delimite($whereTo->getName()) . ' = :' . ($parameterId = 'p_' . sha1($target . microtime(true) . random_bytes(8))), [
							$parameterId => $whereTo->getValue(),
						]);
				}
				throw new QueryBuilderException(sprintf('Unknown where expression [%s] target [%s].', $whereTo->getType(), $target));
			}
		}
