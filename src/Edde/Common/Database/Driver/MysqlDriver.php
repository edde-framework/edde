<?php
	namespace Edde\Common\Database\Driver;

		use Edde\Api\Database\Exception\DriverException;
		use Edde\Common\Database\AbstractPdoDriver;

		class MysqlDriver extends AbstractPdoDriver {
			/**
			 * @inheritdoc
			 */
			public function type(string $type): string {
				switch ($type) {
					case 'string':
						return 'CHARACTER VARYING(1024)';
					case 'text':
						return 'LONGTEXT';
					case 'binary':
						return 'LONGBLOB';
					case 'integer':
						return 'INTEGER';
					case 'float':
						return 'DOUBLE PRECISION';
					case 'bool':
						return 'BOOLEAN';
					case 'datetime':
						return 'DATETIME(6)';
				}
				throw new DriverException(sprintf('Unknown type [%s] for driver [%s]', $type, static::class));
			}
		}
