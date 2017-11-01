<?php
	namespace Edde\Ext\Driver\Database;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Driver\Inject\Driver;
		use Edde\Api\Query\Inject\QueryBuilder;
		use Edde\Api\Query\IQueryBuilder;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\DuplicateTableException;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Api\Storage\Exception\UnknownTableException;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Common\Query\CreateSchemaQuery;
		use Edde\Common\Query\InsertQuery;
		use Edde\Common\Query\NativeQuery;
		use Edde\Common\Query\UpdateQuery;
		use Edde\Common\Schema\SchemaBuilder;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Test\TestCase;

		abstract class AbstractDriverTest extends TestCase {
			use Driver;
			use QueryBuilder;
			/**
			 * @var ISchema
			 */
			protected $schema;

			/**
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function testCreateSchema() {
				$this->driver->execute($this->queryBuilder->query(new CreateSchemaQuery($this->schema)));
				$this->assertTrue(true, 'everything looks nice even here!');
			}

			/**
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function testDuplicateTable() {
				$this->expectException(DuplicateTableException::class);
				$this->driver->execute($this->queryBuilder->query(new CreateSchemaQuery($this->schema)));
			}

			/**
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function testUndefinedTable() {
				$this->expectException(UnknownTableException::class);
				$this->driver->execute(new NativeQuery('SELECT * FROM ' . $this->queryBuilder->delimite('not-exists')));
			}

			/**
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function testInsertQuery() {
				$this->expectException(DuplicateEntryException::class);
				$this->driver->execute($this->queryBuilder->query(new InsertQuery($this->schema->getName(), [
					'guid'                     => '1234',
					'property-for-this-table'  => 'string',
					'this-one-is-not-required' => 12,
				])));
				$this->driver->execute($this->queryBuilder->query(new InsertQuery($this->schema->getName(), [
					'guid'                     => '1235',
					'property-for-this-table'  => 'string',
					'this-one-is-not-required' => 24,
				])));
				$this->driver->execute($this->queryBuilder->query(new InsertQuery($this->schema->getName(), [
					'guid'                     => '1236',
					'property-for-this-table'  => 'string',
					'this-one-is-not-required' => 12,
				])));
			}

			/**
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 * @throws \Exception
			 */
			public function testUpdateQuery() {
				$this->expectException(NullValueException::class);
				$this->driver->execute($this->queryBuilder->query((new UpdateQuery($this->schema->getName(), [
					'property-for-this-table'  => 'string-ex',
					'this-one-is-not-required' => null,
				]))->where()->eq('guid')->to('1235')->query()));
				$this->driver->execute($this->queryBuilder->query((new UpdateQuery($this->schema->getName(), [
					'property-for-this-table'  => null,
					'this-one-is-not-required' => 32,
				]))->where()->eq('guid')->to('1234')->query()));
			}

			/**
			 * @throws ContainerException
			 */
			protected function setUp() {
				/**
				 * parent missing intentionally
				 */
				ContainerFactory::inject($this, [
					IDriver::class       => $this->getDriverFactory(),
					IQueryBuilder::class => $this->getQueryBuilderFactory(),
					new ClassFactory(),
				]);
				$schemaBuilder = new SchemaBuilder('some-cool-schema');
				$schemaBuilder->primary('guid')->type('string');
				$schemaBuilder->string('property-for-this-table')->required();
				$schemaBuilder->integer('this-one-is-not-required')->unique();
				$this->schema = $schemaBuilder->getSchema();
			}

			abstract protected function getDriverFactory();

			abstract protected function getQueryBuilderFactory();
		}
