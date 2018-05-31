<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Query\Commands;
	use Edde\Query\ICommands;
	use Edde\Query\IQuery;

	class Neo4jCompiler extends AbstractCompiler {
		/** @inheritdoc */
		public function compile(IQuery $query): ICommands {
			$commands = new Commands();
			return $commands;
		}
	}
