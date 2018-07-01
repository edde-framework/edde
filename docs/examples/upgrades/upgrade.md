# Example of Upgrade

**Related**: [Upgrades](/edde/upgrades)

Each upgrade should do one kind of action, for example create tables and than do migrations; some engines do not support schema changes and
data manipulation within one transaction, thus it's better to keep smaller upgrades to make higher success rate.

!> Upgrades lives in different namespace (`Upgrade` for logic, `Upgrades` for each upgrade)!

?> **backend/src/Sandbox/Upgrades/ZeroUpgrade.php**

```php
<?php
	declare(strict_types=1);
	namespace Sandbox\Upgrades;
	
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

**Previous**: [Registration](/examples/upgrades/registratoin) | **Next**: [Execution](/examples/upgrades/execution)
