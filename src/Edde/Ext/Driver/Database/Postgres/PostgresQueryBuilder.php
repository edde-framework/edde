<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver\Database\Postgres;

		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Ext\Driver\Database\AbstractSqlBuilder;

		class PostgresQueryBuilder extends AbstractSqlBuilder {
			/**
			 * @inheritdoc
			 */
			public function delimite(string $delimite): string {
				return '"' . str_replace('"', '""', $delimite) . '"';
			}

			/**
			 * @inheritdoc
			 */
			public function type(string $type): string {
				switch (strtolower($type)) {
					case 'string':
						return 'CHARACTER VARYING(1024)';
					case 'text':
						return 'TEXT';
					case 'binary':
						return 'BYTEA';
					case 'int':
						return 'INTEGER';
					case 'float':
						return 'DOUBLE PRECISION';
					case 'bool':
						return 'SMALLINT';
					case 'datetime':
						return 'TIMESTAMP';
				}
				throw new QueryBuilderException(sprintf('Unknown type [%s] in query builder [%s]', $type, static::class));
			}
		}
