<?php
	namespace Edde\Ext\Driver\Database;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Driver\Exception\DriverException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Driver\Inject\Driver;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\DuplicateTableException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Api\Storage\Exception\UnknownTableException;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Common\Query\CreateSchemaQuery;
		use Edde\Common\Query\InsertQuery;
		use Edde\Common\Query\UpdateQuery;
		use Edde\Common\Schema\SchemaBuilder;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Test\TestCase;

		abstract class AbstractDriverTest extends TestCase {
			use Driver;
			/**
			 * @var ISchema
			 */
			protected $schema;

			/**
			 * @throws DriverException
			 */
			public function testCreateSchema() {
				$this->driver->execute(new CreateSchemaQuery($this->schema));
				$this->assertTrue(true, 'everything looks nice even here!');
			}

			/**
			 * @throws DriverException
			 */
			public function testDuplicateTable() {
				$this->expectException(DuplicateTableException::class);
				$this->driver->execute(new CreateSchemaQuery($this->schema));
			}

			/**
			 */
			public function testUndefinedTable() {
				$this->expectException(UnknownTableException::class);
				$this->driver->native('SELECT * FROM notExists');
			}

			/**
			 * @throws DriverException
			 */
			public function testInsertQuery() {
				$this->expectException(DuplicateEntryException::class);
				$this->driver->execute(new InsertQuery($this->schema, [
					'guid'                     => '1234',
					'property-for-this-table'  => 'string',
					'this-one-is-not-required' => 12,
				]));
				$this->driver->execute(new InsertQuery($this->schema, [
					'guid'                     => '1235',
					'property-for-this-table'  => 'string',
					'this-one-is-not-required' => 24,
				]));
				$this->driver->execute(new InsertQuery($this->schema, [
					'guid'                     => '1236',
					'property-for-this-table'  => 'string',
					'this-one-is-not-required' => 12,
				]));
			}

			/**
			 * @throws \Exception
			 */
			public function testUpdateQuery() {
				$this->expectException(NullValueException::class);
				$this->driver->execute(new UpdateQuery($this->schema, [
					'guid'                     => '1235',
					'property-for-this-table'  => 'string-ex',
					'this-one-is-not-required' => null,
				]));
				$this->driver->execute(new UpdateQuery($this->schema, [
					'guid'                     => '1234',
					'property-for-this-table'  => null,
					'this-one-is-not-required' => 32,
				]));
			}

			/**
			 * @throws ContainerException
			 */
			protected function setUp() {
				/**
				 * parent missing intentionally
				 */
				ContainerFactory::inject($this, [
					IDriver::class => $this->getDriverFactory(),
					new ClassFactory(),
				]);
				$schemaBuilder = new SchemaBuilder('some-cool-schema');
				$schemaBuilder->primary('guid')->type('string');
				$schemaBuilder->string('property-for-this-table')->required();
				$schemaBuilder->integer('this-one-is-not-required')->unique();
				$this->schema = $schemaBuilder->getSchema();
			}

			abstract protected function getDriverFactory();
		}
