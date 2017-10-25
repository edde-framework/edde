<?php
	namespace App\Ext\Upgrade;

		use App\Api\User\Schema\GroupSchema;
		use App\Api\User\Schema\RoleSchema;
		use App\Api\User\Schema\UserGroupSchema;
		use App\Api\User\Schema\UserRoleSchema;
		use App\Api\User\Schema\UserSchema;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Upgrade\AbstractUpgrade;

		class ZeroUpgrade extends AbstractUpgrade {
			use SchemaManager;
			use Storage;

			/**
			 * @inheritdoc
			 */
			public function getVersion(): string {
				return '0.0.0.0';
			}

			/**
			 * @inheritdoc
			 */
			public function upgrade(): void {
				$schemaList = [
					UserSchema::class,
					RoleSchema::class,
					GroupSchema::class,
					UserRoleSchema::class,
					UserGroupSchema::class,
				];
				foreach ($schemaList as $schema) {
					$this->storage->createSchema($schema);
				}
			}
		}
