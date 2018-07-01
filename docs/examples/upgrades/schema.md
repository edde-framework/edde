# Schema

**Related**: [Schema](/edde/schema)

Upgrades are tracked in database ([Storage](/edde/storage)) thus it needs schema to describe,
how upgrade table looks like.

?> Because schemas are defined as PHP interfaces, you can use inheritance to move common stuff
to parent interface (like `primary` and  `uuid` property).

!> When a new schema is created, it must be loaded to [SchemaManager](/examples/schema/index).

?> **backend/src/Sandbox/Upgrade/UpgradeSchema.php**

```php
<?php
	declare(strict_types=1);
	namespace Sandbox\Upgrade;
	
	use DateTime;
	
	interface UpgradeSchema {
		const primary = 'uuid';
		/** magical constant, see documentation; basically it makes short name for "UpgradeSchema" to just "upgrade" */
		const alias = true;
		
		/** nope, no autoincrement shit */
		public function uuid($generator = 'uuid'): string;
		
		/** save version name (could be arbitrary string) */
		public function version($unique): string;

		/** when version has been isntalled (important for proper ordering) */
		public function stamp($generator = 'stamp'): DateTime;
	}
```

**Previous**: [Index](/examples/upgrades/index) | **Next**: [Upgrade Manager](/examples/upgrades/upgrade-manager)
