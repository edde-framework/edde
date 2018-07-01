# Upgrade Manager

**Related**: [Upgrades](/edde/upgrades)

Next it's necessary to provide custom `UpgradeManager` implementation. Basic idea is to provide current version, if there is no table yet, create it, and
provide access to all current versions.

There is a hook which could save actually processed version per each version run.

!> `onUpgrade` hook runs outside of a transaction, thus if this method fails, actual upgrade process will be broken. Be careful about this method and 
don't do any dangerous operations there.

?> **backend/src/Sandbox/Upgrade/UpgradeManager.php**

```php
<?php
	declare(strict_types=1);
	namespace Sandbox\Upgrade;

	use Edde\Storage\Entity;
	use Edde\Storage\UnknownTableException;
	use Edde\Upgrade\AbstractUpgradeManager;
	use Edde\Service\Storage\Storage;
	use Edde\Upgrade\IUpgrade;
	use Edde\Upgrade\UpgradeException;
	use Generator;
	use Throwable;

	/**
	 * abstract has already all necessary stuff (like required interface)
	 */
	class UpgradeManager extends AbstractUpgradeManager {
		use Storage;

		/**
		 * this usually needs access to database (storage), thus it's up to you
		 * to provide actual version of an application
		 */
		public function getVersion(): ?string {
			try {
				try {
					foreach ($this->storage->value('SELECT version FROM u:schema ORDER BY stamp DESC', ['$query' => ['u' => UpgradeSchema::class]]) as $version) {
						return $version;
					}
					return null;
				} catch (UnknownTableException $exception) {
					$this->storage->create(UpgradeSchema::class);
					return null;
				}
			} catch (Throwable $exception) {
				throw new UpgradeException(sprintf('Cannot retrieve current version: %s', $exception->getMessage()), 0, $exception);
			}
		}


		/** @inheritdoc */
		public function getCurrentCollection(): Generator {
			return $this->storage->schema(UpgradeSchema::class, 'SELECT * FROM u:schema ORDER BY stamp DESC', ['$query' => ['u' => UpgradeSchema::class]]);
		}

		/** @inheritdoc */
		protected function onUpgrade(IUpgrade $upgrade): void {
			$this->storage->insert(new Entity(UpgradeSchema::class, [
				'version' => $upgrade->getVersion(),
			]));
		}
	}

```

**Previous**: [Schema](/examples/upgrades/schema) | **Next**: [Configurator](/examples/upgrades/configurator)
