<?php
	namespace Edde\Common\Database;

		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Common\Storage\AbstractStorage;

		class DatabaseStorage extends AbstractStorage {
			/**
			 * @inheritdoc
			 */
			public function execute(IQuery $query) {
			}

			/**
			 * @inheritdoc
			 */
			public function native(INativeQuery $nativeQuery) {
			}
		}
