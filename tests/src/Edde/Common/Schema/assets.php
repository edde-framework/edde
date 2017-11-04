<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

	/**
	 * Because there is a reflection loader, we can use php native
	 * fetaures, that's fuckin' cool!
	 */
		interface GuidSchema {
			/**
			 * define property guid which is primary (parameter name matters)
			 */
			public function guid($primary): string;
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
		}

		/**
		 * Schema to test 1:n relations.
		 */
		interface SubBarSchema extends GuidSchema {
			const alias = 'sub-bar';

			public function label(): string;
		}

		interface BarSchema extends GuidSchema {
			const alias = 'bar';

			public function name($unique): string;

			public function label(): ?string;

			/**
			 * define a 1:n relation from Bar to SubBarSchema; subBar property
			 * is connected to guid property of SubBarSchema
			 */
			public function subBar(?SubBarSchema $guid): string;
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
		interface FooBarSchema {
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

		interface BarPooSchema {
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

		interface UserRoleSchema {
			const alias = 'user-role';

			public function user(UserSchema $guid): string;

			public function role(RoleSchema $guid): string;

			public function enabled(): bool;
		}
