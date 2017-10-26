<?php
	namespace Edde\Ext\Driver\Database;

		class PostgresQueryBuilder extends AbstractQueryBuilder {
			/**
			 * @inheritdoc
			 */
			public function delimite(string $delimite): string {
				return '"' . str_replace('"', '""', $delimite) . '"';
			}
		}
