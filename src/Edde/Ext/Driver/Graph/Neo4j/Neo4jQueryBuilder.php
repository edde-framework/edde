<?php
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Common\Query\AbstractQueryBuilder;

		class Neo4jQueryBuilder extends AbstractQueryBuilder {
			/**
			 * @inheritdoc
			 */
			public function delimite(string $delimite): string {
				return $delimite;
			}

			/**
			 * @inheritdoc
			 */
			public function type(string $type): string {
				throw new QueryBuilderException(sprintf('Unknown type [%s] in query builder [%s]', $type, static::class));
			}
		}
