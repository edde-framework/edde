<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver\Database;

		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Api\Query\IInsertQuery;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Query\IUpdateQuery;
		use Edde\Common\Query\AbstractQueryBuilder;
		use Edde\Common\Query\NativeQuery;
		use Edde\Common\Query\NativeTransaction;

		abstract class AbstractSqlBuilder extends AbstractQueryBuilder {
			protected function fragmentCreateSchema(ICrateSchemaQuery $query): INativeTransaction {
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
				return (new NativeTransaction())->query(new NativeQuery($sql . implode(",\n\t", $columnList) . "\n)"));
			}

			protected function fragmentInsert(IInsertQuery $insertQuery): INativeTransaction {
				$nameList = [];
				$parameterList = [];
				foreach ($insertQuery->getSource() as $k => $v) {
					$nameList[] = $this->delimite($k);
					$parameterList['p_' . sha1($k)] = $v;
				}
				$schema = $insertQuery->getSchema();
				$sql = 'INSERT INTO ' . $this->delimite($schema->getName()) . ' (' . implode(',', $nameList) . ') VALUES (';
				return (new NativeTransaction())->query(new NativeQuery($sql . ':' . implode(', :', array_keys($parameterList)) . ')', $parameterList));
			}

			protected function fragmentSelect(ISelectQuery $selectQuery): INativeTransaction {
				$columnList = [];
				$fromList = [];
				foreach ($selectQuery->getSchemaFragmentList() as $schemaFragment) {
					$columnList[$alias] = ($alias = $this->delimite($schemaFragment->getAlias())) . '.*';
					$fromList[$alias] = $this->delimite($schemaFragment->getSchema()->getName()) . ' ' . $alias;
				}
				$sql = "SELECT\n\t" . implode(",\n\t", $columnList) . "\nFROM\n\t" . implode(",\n\t", $fromList);
				return (new NativeTransaction())->query(new NativeQuery($sql));
			}

			protected function fragmentUpdate(IUpdateQuery $updateQuery): INativeTransaction {
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
					$sql .= "WHERE\n\t";
					$this->fragmentWhere($updateQuery->where());
				}
				return (new NativeTransaction())->query(new NativeQuery($sql, $parameterList));
			}

			protected function fragmentWhere(IWhere $whereFragment): INativeTransaction {
			}
		}
