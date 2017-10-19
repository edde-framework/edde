<?php
	namespace Edde\Common\Database\Engine;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Database\Exception\EngineQueryException;
		use Edde\Api\Database\IEngine;
		use Edde\Api\Database\Inject\Engine;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Common\Query\NativeQuery;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Test\TestCase;

		class PostgresEngineTest extends TestCase {
			use Engine;

			/**
			 * @throws EngineQueryException
			 */
			public function testNativeQuery() {
				/**
				 * cleanup public schema by dropping
				 */
				$this->engine->native(new NativeQuery('DROP SCHEMA IF EXISTS "test" CASCADE'));
				$this->engine->native(new NativeQuery('CREATE SCHEMA "test" AUTHORIZATION "edde"'));
			}

			public function testCreateSchema() {
			}

			/**
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			protected function setUp() {
				/**
				 * parent missing intentionally
				 */
				ContainerFactory::inject($this, [
					IEngine::class => ContainerFactory::instance(PostgresEngine::class, ['pgsql:dbname=edde;user=edde;password=edde;host=172.17.0.1']),
					new ClassFactory(),
				]);
			}
		}
