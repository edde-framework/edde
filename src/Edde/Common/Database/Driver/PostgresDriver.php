<?php
	namespace Edde\Common\Database\Driver;

		use Edde\Api\Database\Exception\DriverException;
		use Edde\Api\Database\Exception\DriverQueryException;
		use Edde\Api\Query\INativeQuery;
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
					throw new DriverQueryException($exception->getMessage(), 0, $exception);
				}
			}

			/**
			 * @inheritdoc
			 */
			public function type(string $type): string {
				switch ($type) {
				}
				throw new DriverException(sprintf('Unknown type [%s] for driver [%s]', $type, static::class));
			}
		}
