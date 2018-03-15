<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

	/**
	 * Because there is a reflection loader, we can use php native
	 * features, that's fuckin' cool!
	 */
	interface UuidSchema {
		/**
		 * define property guid which is primary (parameter name matters)
		 */
		public function uuid($primary): string;
	}

	/**
	 * This is the core of this genius idea: use native PHP features!
	 */
	interface RelationSchema extends UuidSchema {
		const relation = true;
	}

	/**
	 * Define a Foo schema extended from guid...
	 */
	interface FooSchema extends UuidSchema {
		const alias = 'foo';

		/**
		 * ...with unique string name
		 */
		public function name($unique): string;

		/**
		 * ...and optional string label
		 */
		public function label(): ?string;

		/**
		 * define a 1:n relation from this to PooSchema; poo property
		 * is connected to guid property of PooSchema
		 *
		 * the relation is optional as there is ?string
		 */
		public function poo(PooSchema $uuid): ?string;
	}

	interface BarSchema extends UuidSchema {
		const alias = 'bar';

		public function name($unique): string;

		public function label(): ?string;

		public function poo(PooSchema $uuid): ?string;
	}

	interface PooSchema extends UuidSchema {
		const alias = 'poo';

		public function name($unique): string;

		public function label(): ?string;
	}

	/**
	 * This schema has exactly two links, thus this will be pure
	 * relation (m:n) schema.
	 */
	interface FooBarSchema extends RelationSchema {
		const alias = 'foo-bar';

		/**
		 * make property foo as a reference to FooSchema's property $uuid
		 */
		public function foo(FooSchema $uuid): string;

		/**
		 * the same works here
		 */
		public function bar(BarSchema $uuid): string;
	}

	interface BarPooSchema extends RelationSchema {
		const alias = 'bar-poo';

		public function bar(BarSchema $uuid): string;

		public function poo(PooSchema $uuid): string;
	}

	interface SimpleSchema extends UuidSchema {
		const alias = 'simple';

		public function name(): string;

		public function optional(): ?string;

		public function value(): ?float;

		public function date(): ?\DateTime;

		public function question(): ?bool;
	}

	interface UserSchema extends UuidSchema {
		const alias = 'user';

		public function name($unique): string;

		public function email($unique): string;

		public function created(): \DateTime;
	}

	interface RoleSchema extends UuidSchema {
		const alias = 'role';

		public function name($unique): string;

		public function label(): ?string;
	}

	interface UserRoleSchema extends RelationSchema {
		const alias = 'user-role';

		public function user(UserSchema $uuid): string;

		public function role(RoleSchema $uuid): string;

		public function enabled($default = true): bool;
	}

	interface SourceSchema extends UuidSchema {
		public function name($unique): string;
	}

	interface TargetSchema extends UuidSchema {
		public function name($unique): string;
	}

	interface SourceOneTargetSchema extends RelationSchema {
		public function source(SourceSchema $uuid): string;

		public function target(TargetSchema $uuid): string;
	}

	interface SourceTwoTargetSchema extends RelationSchema {
		const alias = 'source-two-target';

		public function source(SourceSchema $uuid): string;

		public function target(TargetSchema $uuid): string;
	}
