<?php
	namespace App\Ext\Upgrade;

		use App\Api\User\Schema\RoleSchema;
		use App\Api\User\Schema\UserSchema;
		use Edde\Api\Entity\Inject\EntityManager;
		use Edde\Common\Upgrade\AbstractUpgrade;

		class ZeroSeedUpgrade extends AbstractUpgrade {
			use EntityManager;

			/**
			 * @inheritdoc
			 */
			public function getVersion(): string {
				return '0.0.0.1';
			}

			/**
			 * @inheritdoc
			 */
			public function upgrade(): void {
				$role = $this->entityManager->create(RoleSchema::class, [
					'name' => 'root',
				])->save();
				$this->entityManager->create(RoleSchema::class, [
					'name' => 'guest',
				])->save();
				$user = $this->entityManager->create(UserSchema::class, [
					'name'     => 'root',
					'password' => '1234',
				]);
				$user->attach($role);
				$user->save();
			}
		}
