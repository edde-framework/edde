<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Container\ContainerException;
	use Edde\Job\JobManagerSchema;
	use Edde\Job\JobSchema;
	use Edde\Service\Schema\SchemaManager;
	use Edde\TestCase;
	use Edde\Upgrade\UpgradeSchema;
	use InvalidFilterSchema;
	use InvalidGeneratorSchema;
	use InvalidMetaSchema;
	use InvalidPrimarySchema;
	use InvalidRelationSchema;
	use InvalidTypeSchema;
	use InvalidValidatorSchema;
	use NoPrimaryKeySchema;
	use ProjectMemberSchema;
	use ProjectSchema;

	class SchemaManagerTest extends TestCase {
		use SchemaManager;

		/**
		 * @throws SchemaException
		 */
		public function testLoadException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Requested schema [nope] is not loaded; try to use [Edde\Schema\ISchemaManager::load("nope")]');
			$this->schemaManager->getSchema('nope');
		}

		/**
		 * @throws SchemaException
		 */
		public function testHasSchema() {
			$this->schemaManager->load(ProjectMemberSchema::class);
			self::assertFalse($this->schemaManager->hasSchema('foo'));
			self::assertTrue($this->schemaManager->hasSchema(ProjectMemberSchema::class));
		}

		public function testGetSchemas() {
			$expect = [
				UpgradeSchema::class       => UpgradeSchema::class,
				'upgrade'                  => UpgradeSchema::class,
				JobSchema::class           => JobSchema::class,
				'job'                      => JobSchema::class,
				JobSchema::class           => JobManagerSchema::class,
				'job-manager'              => JobManagerSchema::class,
				ProjectMemberSchema::class => ProjectMemberSchema::class,
				'project-member'           => ProjectMemberSchema::class,
				ProjectSchema::class       => ProjectSchema::class,
				'project'                  => ProjectSchema::class,
			];
			$actual = [];
			foreach ($this->schemaManager->getSchemas() as $name => $schema) {
				$actual[$name] = $schema->getName();
			}
			self::assertSame($expect, $actual);
		}

		/**
		 * @throws SchemaException
		 */
		public function testInvalidMetaSchema() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Meta for schema [InvalidMetaSchema] must be an array.');
			$this->schemaManager->load(InvalidMetaSchema::class);
		}

		/**
		 * @throws SchemaException
		 */
		public function testNoPrimaryKeySchema() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Schema [NoPrimaryKeySchema] has no primary property; please define one (you can extend [Edde\Schema\UuidSchema] to use default uuid support).');
			$this->schemaManager->load(NoPrimaryKeySchema::class);
		}

		/**
		 * @throws SchemaException
		 */
		public function testInvalidGenerator() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Parameter [InvalidGeneratorSchema::mrdka($generator)] must have a default string value as a generator name.');
			$this->schemaManager->load(InvalidGeneratorSchema::class);
		}

		/**
		 * @throws SchemaException
		 */
		public function testInvalidFilter() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Parameter [InvalidFilterSchema::mrdka($filter)] must have a default string value as a filter name.');
			$this->schemaManager->load(InvalidFilterSchema::class);
		}

		/**
		 * @throws SchemaException
		 */
		public function testInvalidValidator() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Parameter [InvalidValidatorSchema::mrdka($validator)] must have a default string value as a validator name.');
			$this->schemaManager->load(InvalidValidatorSchema::class);
		}

		/**
		 * @throws SchemaException
		 */
		public function testInvalidType() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Parameter [InvalidTypeSchema::mrdka($type)] must have a default string value as a type name.');
			$this->schemaManager->load(InvalidTypeSchema::class);
		}

		/**
		 * @throws SchemaException
		 */
		public function testInvalidRelation() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Target [foo] or source [bar] property of relation is not present in schema [InvalidRelationSchema].');
			$this->schemaManager->load(InvalidRelationSchema::class);
		}

		/**
		 * @throws SchemaException
		 */
		public function testInvalidPrimary() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Primary property [InvalidPrimarySchema::blabla] is defined, but property does not exist; please add corresponding method to schema.');
			$this->schemaManager->load(InvalidPrimarySchema::class);
		}

		/**
		 * @throws ContainerException
		 * @throws SchemaException
		 */
		protected function setUp() {
			parent::setUp();
			$this->schemaManager->loads([
				ProjectMemberSchema::class,
				ProjectSchema::class,
			]);
		}
	}
