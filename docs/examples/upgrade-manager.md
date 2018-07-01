# Sample Upgrade Manager

**Related**: [Upgrades](/edde/upgrades), [Configurators](/edde/configurators)

## Prolog

Edde does not provide Upgrade Manager by default as there is just abstract implementation
for easy integration.

Use `AbstractUpgradeManager` as the base class for you implementation, bind it against `IUpgradeManager`
interface and you are happy!

?> Code here is basically ready to use, so you can copy-paste-run it only with little adjustments.

## Schema

At first, we need schema to save information about upgrades already installed. Schema will be created automagically.

?> Because schemas are defined as PHP interfaces, you can use inheritance to move common stuff to parent interface (like `primary` and 
`uuid` property).

?> **src\Fooplication\Upgrade\UpgradeSchema.php**

```php
<?php
	declare(strict_types=1);
	namespace Fooplication\Upgrade;
	
	use DateTime;
	
	interface UpgradeSchema {
		const primary = 'uuid';
		const alias = true;
		
		/** nope, no autoincrement shit */
		public function uuid($generator = 'uuid'): string;
		
		/** save version name (could be arbitrary string) */
		public function version($unique): string;

		/** when version has been isntalled (important for proper ordering) */
		public function stamp($generator = 'stamp'): DateTime;
	}
```

## UpgradeManager

Next it's necessary to provide custom `UpgradeManager` implementation. Basic idea is to provide current version, if there is no table yet, create it, and
provide access to all current versions.

There is a hook which could save actually processed version per each version run.

!> `onUpgrade` hook is run outside of a transaction, thus if this method fails, actual upgrade process will be broken. Be careful about this method and 
don't do any dangerous operations there.

?> **src\Fooplication\Upgrade\UpgradeManager.php**

```php
<?php
	declare(strict_types=1);
	namespace Fooplication\Upgrade;
	
	use Edde\Upgrade\AbstractUpgradeManager;
	use Edde\Service\Storage\Storage;
	use Generator;
	
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

## Configurator

This is a bit magical part: Create a Configurator responsible for setting up `UpgradeManager` you just created and register it for setup.

?> **src\Fooplication\Configurators\UpgradeManagerConfigurator.php**

```php
<?php
	declare(strict_types=1);
	namespace Fooplication\Configurators;
	
	use Edde\Configurable\AbstractConfigurator;
	use Edde\Service\Container\Container;
	use Edde\Upgrade\IUpgradeManager;
	
	class UpgradeManagerConfigurator extends AbstractConfigurator {
		use Container;
		
		/** @var $instance IUpgradeManager */
		public function configure($instance){
			/** it's good practice to leave parent configurator here */
            parent::configure($instance);
			$upgrades = [
				// ordered list of your upgrades; just can grow, NEVER change order of upgrades
			];
			foreach ($upgrades as $upgrade) {
				$instance->registerUpgrade($this->container->create($upgrade, [], __METHOD__));
			}
		}
	}
```

Now it's time to see `loader.php`:

```php
<?php
	declare(strict_types=1);
	
	use Edde\Container\ContainerFactory;
	use Edde\Upgrade\IUpgradeManager;
	use Fooplication\Upgrade\UpgradeManager;
	
	// ... shortened...
	
	return ContainerFactory::container([
		/** interface => implementation bindings... */
		// ... 
		/** register you new nice and shiny upgrade manager */
		IUpgradeManager::class => UpgradeManager::class,
		// ... 
	], [
		/** configurator bindings */
		// ...
		/**
         * register configurator for your upgrade manager; when somebody touch it, configurator will be executed and prepare
         * upgrade manager for use  
         */
		IUpgradeManager::class => UpgradeManagerConfigurator::class,
		// ...
	]);

```

## Example of Upgrade class

Each upgrade should do one kind of action, for example create tables and than do migrations; some engines do not support schema changes and
data manipulation within one transaction, thus it's better to keep smaller upgrades to make higher success rate.

?> **src\Fooplication\Upgrades\ZeroUpgrade.php**

```php
<?php
	declare(strict_types=1);
	namespace Fooplication\Upgrades;
	
	use Edde\Upgrade\AbstractUpgrade;
	
	/**
	 * Zero upgrade because an application is in it's very first state (zero state). 
     */
	class ZeroUpgrade extends AbstractUpgrade {
		/** use any service you need as everything is available */
		
		public function getVersion(): string {
			/**
             * return whatever version you want; it could even be 'fluffy pig'; version as a content is not used for any parsing, order
             * is done by the order specified in configurator 
             */
			return '1.0.0';
        }
        
		public function upgrade(): void {
			/** time for you magic here ;) */
        }
	}
```

## How to use the magic

Edde contains default CLI implementation for an upgrade manager:

```bash
# run upgrade up to current version available in the application
$ cli upgrade.upgrade/upgrade

# see current version of the application
$ cli upgrade.upgrade/version
```

!> To make this work, you have to have registered `CascadeFactory()` in you `loader.php`!
