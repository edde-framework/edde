# Exceptions

Edde follows simple exception model: every namespace has it's own root exception, 
all extended from `EddeException`, thus it's simple to catch whatever you need
without messing with complex model.

As a general recommendation you should use in your application similar approach -
for example you could have root exception and all other extend from it.

```php
<?php
	declare(strict_types=1);
	namespace Fooplication;

	use Exception;
	
	class FooplicationException extends Exception {
	}
```
