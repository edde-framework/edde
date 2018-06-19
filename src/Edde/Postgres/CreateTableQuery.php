<?php
	declare(strict_types=1);
	namespace Edde\Postgres;

	use Edde\Query\AbstractCreateTableQuery;

	class CreateTableQuery extends AbstractCreateTableQuery {
		public function __construct(array $types = null) {
			parent::__construct($types ?: [
				'string'   => 'CHARACTER VARYING(1024)',
				'text'     => 'TEXT',
				'binary'   => 'BYTEA',
				'int'      => 'INTEGER',
				'float'    => 'DOUBLE PRECISION',
				'bool'     => 'SMALLINT',
				'datetime' => 'TIMESTAMP(6)',
			]);
		}
	}
