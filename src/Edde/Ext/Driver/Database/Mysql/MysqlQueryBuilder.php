<?php
	namespace Edde\Ext\Driver\Database\Mysql;

		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Ext\Driver\Database\AbstractSqlBuilder;

		class MysqlQueryBuilder extends AbstractSqlBuilder {
			public function delimite(string $delimite): string {
				return '`' . str_replace('`', '``', $delimite) . '`';
			}

			/**
			 * @inheritdoc
			 */
			public function type(string $type): string {
				switch (strtolower($type)) {
					case 'string':
						return 'CHARACTER VARYING(1024)';
					case 'text':
						return 'LONGTEXT';
					case 'binary':
						return 'LONGBLOB';
					case 'int':
						return 'INTEGER';
					case 'float':
						return 'DOUBLE PRECISION';
					case 'bool':
						return 'TINYINT';
					case 'datetime':
						return 'DATETIME(6)';
				}
				throw new QueryBuilderException(sprintf('Unknown type [%s] in query builder [%s]', $type, static::class));
			}
		}
