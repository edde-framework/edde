<?php
	namespace Edde\Common\Database\Engine;

		use Edde\Api\Database\Exception\EngineQueryException;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Common\Database\AbstractPdoEngine;

		class PostgresEngine extends AbstractPdoEngine {
			/**
			 * @inheritdoc
			 */
			public function execute(IQuery $query) {
			}

			/**
			 * @inheritdoc
			 */
			public function native(INativeQuery $nativeQuery) {
				try {
					$prepared = $this->pdo->prepare($nativeQuery->getQuery());
					$prepared->execute($nativeQuery->getParameterList());
					return $prepared;
				} catch (\PDOException $exception) {
					throw new EngineQueryException($exception->getMessage(), 0, $exception);
				}
			}
		}
