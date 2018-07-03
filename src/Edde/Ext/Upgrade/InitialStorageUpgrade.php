<?php
	declare(strict_types=1);

	namespace Edde\Ext\Upgrade;

	use Edde\Api\Schema\LazySchemaManagerTrait;
	use Edde\Api\Storage\LazyStorageTrait;
	use Edde\Common\Query\Schema\CreateSchemaQuery;
	use Edde\Common\Upgrade\AbstractUpgrade;

	/**
	 * This upgrade is useful for initial storage setup; it will create all available schemas.
	 */
	class InitialStorageUpgrade extends AbstractUpgrade {
		use LazyStorageTrait;
		use LazySchemaManagerTrait;

		/**
		 * @param string $version
		 */
		public function __construct($version = 'edde') {
			parent::__construct($version);
		}

		protected function onUpgrade() {
			$this->schemaManager->setup();
			foreach ($this->schemaManager->getSchemaList() as $schema) {
				if ($schema->getMeta('storable', false)) {
					$this->storage->execute(new CreateSchemaQuery($schema));
				}
			}
		}
	}
