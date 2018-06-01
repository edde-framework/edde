<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Query\IQuery;

	class Neo4jCompiler extends AbstractCompiler {
		/** @inheritdoc */
		public function compile(IQuery $query): string {
		}

		/** @inheritdoc */
		public function delimit(string $delimit): string {
			return '`' . str_replace('`', '`' . '`', $delimit) . '`';
		}
	}
