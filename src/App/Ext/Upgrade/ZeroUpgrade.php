<?php
	declare(strict_types=1);
	namespace App\Ext\Upgrade;

		use App\Api\User\Schema\RoleSchema;
		use App\Api\User\Schema\UserRoleSchema;
		use App\Api\User\Schema\UserSchema;
		use Edde\Api\Entity\Inject\EntityManager;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Storage\Query\CreateSchemaQuery;
		use Edde\Common\Upgrade\AbstractUpgrade;

		class ZeroUpgrade extends AbstractUpgrade {
			use SchemaManager;
			use EntityManager;
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
					UserRoleSchema::class,
				];
				foreach ($schemaList as $schema) {
					$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load($schema)));
				}
			}
		}
