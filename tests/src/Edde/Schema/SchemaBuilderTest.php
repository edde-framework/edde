<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\TestCase;

	class SchemaBuilderTest extends TestCase {
		public function testSchemaBuilder() {
			$schemaBuilder = new SchemaBuilder('Boo');
			$schemaBuilder->property('boo')->primary();
			$schemaBuilder->meta($meta = ['a' => true]);
			self::assertSame($schema = $schemaBuilder->create(), $schemaBuilder->create());
			self::assertEquals(true, $schema->getMeta('a'));
		}

		/**
		 * @throws SchemaException
		 */
		public function testAttributeException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Requested unknown attribute [Boo::nope].');
			$schemaBuilder = new SchemaBuilder('Boo');
			$schemaBuilder->property('boo')->primary();
			$schemaBuilder->meta($meta = ['a' => true]);
			$schema = $schemaBuilder->create();
			$schema->getAttribute('nope');
		}

		public function testUniques() {
			$schemaBuilder = new SchemaBuilder('Boo');
			$schemaBuilder->property('boo')->primary();
			$schemaBuilder->meta($meta = ['a' => true]);
			$schemaBuilder->property('name')->unique();
			$schemaBuilder->property('unique')->unique();
			$schema = $schemaBuilder->create();
			self::assertEquals([
				'name'   => new Attribute((object)['name' => 'name', 'unique' => true, 'required' => true]),
				'unique' => new Attribute((object)['name' => 'unique', 'unique' => true, 'required' => true]),
			], $schema->getUniques());
			/**
			 * just heat coverage
			 */
			$schema->getUniques();
		}

		/**
		 * @throws SchemaException
		 */
		public function testSourceException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Schema [Boo] is not relation; relation source is not available!');
			$schemaBuilder = new SchemaBuilder('Boo');
			$schemaBuilder->property('boo')->primary();
			$schema = $schemaBuilder->create();
			$schema->getSource();
		}

		/**
		 * @throws SchemaException
		 */
		public function testTargetException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Schema [Boo] is not relation; relation target is not available!');
			$schemaBuilder = new SchemaBuilder('Boo');
			$schemaBuilder->property('boo')->primary();
			$schema = $schemaBuilder->create();
			$schema->getTarget();
		}

		/**
		 * @throws SchemaException
		 */
		public function testCheckRelationException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Invalid relation (Boo)-[!Boo]->(Boo): Relation schema [Boo] is not a relation.');
			$schemaBuilder = new SchemaBuilder('Boo');
			$schemaBuilder->property('boo')->primary();
			$schema = $schemaBuilder->create();
			$schema->checkRelation($schema, $schema);
		}

		/**
		 * @throws SchemaException
		 */
		public function testCheckRelationOrderException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Invalid relation (foo)-[boo relation]->(!bar): Target schema [bar] differs from expected relation [b]; did you swap $source and $target schema?.');
			$schemaBuilder = new SchemaBuilder('boo relation');
			$schemaBuilder->relation('a', 'b');
			$schemaBuilder->property('uuid')->primary();
			$schemaBuilder->property('a')->schema('foo');
			$schemaBuilder->property('b')->schema('b');
			$booRelation = $schemaBuilder->create();
			$schemaBuilder = new SchemaBuilder('foo');
			$schemaBuilder->property('uuid')->primary();
			$foo = $schemaBuilder->create();
			$schemaBuilder = new SchemaBuilder('bar');
			$schemaBuilder->property('uuid')->primary();
			$bar = $schemaBuilder->create();
			self::assertTrue($booRelation->isRelation());
			$booRelation->checkRelation($foo, $bar);
		}
	}
