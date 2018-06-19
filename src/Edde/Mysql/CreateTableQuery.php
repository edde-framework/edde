<?php
	declare(strict_types=1);
	namespace Edde\Mysql;

	use Edde\Query\AbstractCreateTableQuery;

	class CreateTableQuery extends AbstractCreateTableQuery {
		public function __construct(array $types = null) {
			parent::__construct($types ?: [
				'string'   => 'CHARACTER VARYING(1024)',
				'text'     => 'LONGTEXT',
				'binary'   => 'LONGBLOB',
				'int'      => 'INTEGER',
				'float'    => 'DOUBLE PRECISION',
				'bool'     => 'TINYINT',
				'datetime' => 'DATETIME(6)',
			]);
		}
	}
