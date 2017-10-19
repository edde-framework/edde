<?php
	namespace Edde\Common\Database\Engine;

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
		}
