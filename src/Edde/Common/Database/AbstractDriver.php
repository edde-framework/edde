<?php
	namespace Edde\Common\Database;

		use Edde\Api\Database\IDriver;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Common\Object\Object;

		/**
		 * Driver is not based on PDO intentionally as there are other drivers which
		 * could use different engines (like neo4j),
		 */
		abstract class AbstractDriver extends Object implements IDriver {
			use NativeQueryBuilder;

			/**
			 * @inheritdoc
			 */
			public function execute(IQuery $query) {
				return $this->native($this->toNative($query));
			}

			/**
			 * @inheritdoc
			 */
			public function toNative(IQuery $query): INativeQuery {
				return $this->fragment($query->getQuery());
			}

			/**
			 * @inheritdoc
			 */
			public function delimite(string $delimite): string {
				return '"' . str_replace('"', '""', $delimite) . '"';
			}
		}
