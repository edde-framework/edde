<?php
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\INativeQuery;
		use Edde\Common\Driver\AbstractDriver;

		class Neo4jDriver extends AbstractDriver {
			/**
			 * @inheritdoc
			 */
			public function execute(INativeQuery $nativeQuery) {
			}

			/**
			 * @inheritdoc
			 */
			public function start(): IDriver {
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): IDriver {
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function rollback(): IDriver {
				return $this;
			}
		}
