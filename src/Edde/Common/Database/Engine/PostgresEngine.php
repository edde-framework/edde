<?php
	namespace Edde\Common\Database\Engine;

		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Common\Database\AbstractPdoEngine;

		class PostgresEngine extends AbstractPdoEngine {
			public function execute(IQuery $query) {
			}

			public function native(INativeQuery $nativeQuery) {
			}
		}
