<?php
	namespace Edde\Ext\Driver\Database;

		class MysqlQueryBuilder extends AbstractQueryBuilder {
			public function delimite(string $delimite): string {
				return '`' . str_replace('`', '``', $delimite) . '`';
			}
		}
