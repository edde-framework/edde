<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

	/**
	 * Because there is a reflection loader, we can use php native
	 * features, that's fuckin' cool!
	 */
		interface GuidSchema {
			/**
			 * define property guid which is primary (parameter name matters)
			 */
			public function guid($primary): string;
		}

		/**
		 * This is the core of this genius idea: use native PHP features!
		 */
		interface RelationSchema extends GuidSchema {
			const relation = true;
		}

		/**
		 * Define a Foo schema extended from guid...
		 */
		interface FooSchema extends GuidSchema {
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
			public function poo(PooSchema $guid): ?string;
		}

		interface BarSchema extends GuidSchema {
			const alias = 'bar';

			public function name($unique): string;

			public function label(): ?string;
		}

		interface PooSchema extends GuidSchema {
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
			 * make property foo as a reference to FooSchema's property $guid
			 */
			public function foo(FooSchema $guid): string;

			/**
			 * the same works here
			 */
			public function bar(BarSchema $guid): string;
		}

		interface BarPooSchema extends RelationSchema {
			const alias = 'bar-poo';

			public function bar(BarSchema $guid): string;

			public function poo(PooSchema $guid): string;
		}

		interface SimpleSchema extends GuidSchema {
			const alias = 'simple';

			public function name(): string;

			public function optional(): ?string;

			public function value(): ?float;

			public function date(): ?\DateTime;

			public function question(): ?bool;
		}

		interface UserSchema extends GuidSchema {
			const alias = 'user';

			public function name(): string;

			public function email($unique): string;

			public function created(): \DateTime;
		}

		interface RoleSchema extends GuidSchema {
			const alias = 'role';

			public function name($unique): string;

			public function label(): ?string;
		}

		interface UserRoleSchema extends RelationSchema {
			const alias = 'user-role';

			public function user(UserSchema $guid): string;

			public function role(RoleSchema $guid): string;

			public function enabled(): bool;
		}
