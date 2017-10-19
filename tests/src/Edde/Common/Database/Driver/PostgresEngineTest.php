<?php
	namespace Edde\Common\Database\Driver;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Database\Exception\DriverQueryException;
		use Edde\Api\Database\Exception\NativeQueryException;
		use Edde\Api\Database\IDriver;
		use Edde\Api\Database\Inject\Driver;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Common\Query\CreateSchemaQuery;
		use Edde\Common\Query\InsertQuery;
		use Edde\Common\Query\NativeQuery;
		use Edde\Common\Query\SelectQuery;
		use Edde\Common\Query\UpdateQuery;
		use Edde\Common\Schema\Schema;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Test\TestCase;

		class PostgresEngineTest extends TestCase {
			use Driver;
			/**
			 * @var ISchema
			 */
			protected $schema;

			/**
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function testNativeQuery() {
				/**
				 * cleanup public schema by dropping
				 */
				$this->driver->native(new NativeQuery('DROP SCHEMA IF EXISTS "test" CASCADE'));
				$this->driver->native(new NativeQuery('CREATE SCHEMA "test" AUTHORIZATION "edde"'));
				$this->assertTrue(true, 'everything looks nice here!');
			}

			/**
			 * @throws NativeQueryException
			 * @throws \Exception
			 */
			public function testSelectQuery() {
				// @formatter:off
				$nativeQuery = $this->driver->toNative((new SelectQuery())->
					table('some-table-name', 'aaa')->
						column('foo')->
						all()->
					table('another-table')->
						column('bar')->
					where()->
						eq('foo')->to('bound parameter!')->and()->
						eq('foo')->toColumn('bar')->or()->
						neq('franta')->to('betka')->or()->
							group()->
								gt('a')->than(5)->and()->
								neq('foo')->to('bar')->
							end()->or()->
						gt('blah')->than('foo')->and()->
						in('enum-column')->select(
							(new SelectQuery())->table('moo')->all()->query()
						)->and()->
						eq('a')->to('b')->
					query());
				// @formatter:on
			}

			/**
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function testCreateSchema() {
				$this->driver->execute(new CreateSchemaQuery($this->schema));
				$this->assertTrue(true, 'everything looks nice even here!');
			}

			/**
			 * @throws DriverQueryException
			 * @throws IntegrityException
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
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function testUpdateQuery() {
				$update = new UpdateQuery($this->schema, [
					'property-for-this-table'  => 'string-ex',
					'this-one-is-not-required' => null,
				]);
				$this->driver->execute($update);
			}

			/**
			 * @throws ContainerException
			 * @throws DriverQueryException
			 * @throws FactoryException
			 * @throws IntegrityException
			 */
			protected function setUp() {
				/**
				 * parent missing intentionally
				 */
				ContainerFactory::inject($this, [
					IDriver::class => ContainerFactory::instance(PostgresDriver::class, ['pgsql:dbname=edde;user=edde;password=edde;host=172.17.0.1']),
					new ClassFactory(),
				]);
				$this->driver->native(new NativeQuery('SET search_path TO "test"'));
				$this->schema = $schema = Schema::create('some-cool-schema');
				$schema->primary('guid')->type('string');
				$schema->string('property-for-this-table')->required();
				$schema->integer('this-one-is-not-required')->unique();
			}
		}
