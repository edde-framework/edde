<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

	use Edde\Schema\AliasSchema;
	use Edde\Schema\RelationSchema;
	use Edde\Schema\UuidSchema;

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
	interface FooBarSchema extends RelationSchema, AliasSchema {
		/**
		 * make property foo as a reference to FooSchema's property $uuid
		 */
		public function foo(FooSchema $uuid): string;

		/**
		 * the same works here
		 */
		public function bar(BarSchema $uuid): string;
	}

	interface BarPooSchema extends RelationSchema, AliasSchema {
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

	interface SourceTwoTargetSchema extends RelationSchema, AliasSchema {
		public function source(SourceSchema $uuid): string;

		public function target(TargetSchema $uuid): string;
	}
