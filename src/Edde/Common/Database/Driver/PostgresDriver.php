<?php
	namespace Edde\Common\Database\Driver;

		use Edde\Api\Database\Exception\DriverException;
		use Edde\Api\Database\Exception\DriverQueryException;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Common\Database\AbstractPdoDriver;

		class PostgresDriver extends AbstractPdoDriver {
			/**
			 * @inheritdoc
			 */
			public function native(INativeQuery $nativeQuery) {
				try {
					$prepared = $this->pdo->prepare($nativeQuery->getQuery());
					$prepared->execute($nativeQuery->getParameterList());
					return $prepared;
				} catch (\PDOException $exception) {
					if (stripos($message = $exception->getMessage(), 'unique') !== false) {
						throw new DuplicateEntryException($message, 0, $exception);
					}
					throw new DriverQueryException($message, 0, $exception);
				}
			}

			/**
			 * @inheritdoc
			 */
			public function type(string $type): string {
				switch ($type) {
					case 'string':
						return 'character varying(1024)';
					case 'text':
						return 'text';
					case 'integer':
						return 'integer';
					case 'float':
						return 'double precision';
				}
				throw new DriverException(sprintf('Unknown type [%s] for driver [%s]', $type, static::class));
			}
		}
