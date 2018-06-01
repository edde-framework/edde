<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Query\Binds;
	use Edde\Query\Command;
	use Edde\Query\ICommand;
	use Edde\Query\IQuery;

	class Neo4jCompiler extends AbstractCompiler {
		/** @inheritdoc */
		public function compile(IQuery $query, array $params = []): ICommand {
			return new Command('', new Binds());
		}

		/** @inheritdoc */
		public function delimit(string $delimit): string {
			return;
		}
	}
