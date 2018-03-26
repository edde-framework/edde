<?php
	declare(strict_types=1);
	use Edde\Schema\AliasSchema;
	use Edde\Schema\RelationSchema;
	use Edde\Schema\UuidSchema;

	interface SimpleSchema extends UuidSchema, AliasSchema {
		public function name($unique): string;
	}

	interface FooSchema extends UuidSchema {
	}

	interface BarSchema extends UuidSchema {
	}

	interface FooBarSchema extends RelationSchema, AliasSchema {
		public function foo(FooSchema $uuid): string;

		public function bar(BarSchema $uuid): string;
	}
