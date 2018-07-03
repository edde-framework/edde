# Principles

There are several principles used in Edde to keep framework in some shape. They're selected by experience,
you can use different approach but ones described here are in line with philosophy of Edde.

?> In general, Edde is following [SOLID](https://en.wikipedia.org/wiki/SOLID).

## Single Responsibility Principle

Probably most important principle, also related to **KISS**: when creating an application, it's very simple to
put a lot of stuff in a few classes, which become a bit heavier over time or one method doing more things at once.

A little example:

```php
<?php
	declare(strict_types=1);

	function trim(string $str): string {
		$str = (string)str_replace(["\r\n","\r",], "\n", $str);
		return trim($str);
}
```

When you call this method, it's doing more things which could cause some surprises - so for example, when you
trim http headers (which are using `\r\n`), you'll get strange output.

On a higher level, even it usually costs more classes to create, it's better to break things into small pieces. 

Let's have an example from [Sandbox](/getting-started/index) tutorial:

```php
<?php
	declare(strict_types=1);
	
	use Edde\Storage\IEntity;
	
	interface UserService {
		// ...
	}
		
	interface ProjectService {
		// ...
	}
	
	interface ProjectMemberService {
		/**
         * attach user to project using m:n relation
         */
		public function attach(string $project, string $user): IEntity;
		
		/**
         * detach given user from the project
         */
		public function detach(string $project, string $user): IEntity;
		
		/**
         * attach exactly one user to a project, detach all others
         */
		public function link(string $project, string $user): IEntity;
		
		/**
         * get project members by project idenfitier (uuid, ...)
         */
		public function getMembers(string $project): Generator;
		
		/**
         * get projects of the given member
         */
		public function getProjects(string $user): Generator;
	}
``` 

User and Project services are single responsible - none of them are implementing relation code as it does not belongs there.
`ProjectMemberService` - in the cost of another class - is responsible for maintaining relations and queries related to it.
As you can see, both classes are clean and simple and all the "heavy" code is in the appropriate place.

?> This is just concept, how you can write your application to follow this principle. It could be considered a good practice,
but remember that nothing written should say, how your application should look.

## Kiss

This is closely related to SRP as when pieces of code does just little thing, it's also simple. If you are not experienced
and it's harder to see, what is single responsibility, you can see principle from this point of view: simplicity.

When there is a method with more than 3 parameters, it could be considered wrong. When there is a method with a boolean
parameter, it is [probably wrong](https://ariya.io/2011/08/hall-of-api-shame-boolean-trap). If your method has a lot of
branches (`if`s), `switch`es or a lot of lines in general, there is probably problem.

All of these things are pointing to both principles: KISS and SRP which you are probably breaking, if you can detect such
pieces of code.

?> A more pieces of code may look worse, but in general it's much more maintainable and testable than bigger chunks of an
application. It's hard concept and it requires high discipline to follow it as the API of an application could be a bit
more complex too. 
