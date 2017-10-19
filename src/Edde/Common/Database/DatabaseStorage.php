<?php
	namespace Edde\Common\Database;

		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Common\Storage\AbstractStorage;

		class DatabaseStorage extends AbstractStorage {
			use Driver;

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
