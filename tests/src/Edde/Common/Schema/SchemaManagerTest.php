<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Schema\Exception\UnknownSchemaException;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Ext\Test\TestCase;

		class SchemaManagerTest extends TestCase {
			use SchemaManager;

			/**
			 * @throws UnknownSchemaException
			 */
			public function testRelationSchema() {
				$fooBarSchema = $this->schemaManager->load(FooBarSchema::class);
				self::assertTrue($fooBarSchema->isRelation(), 'foo bar schema should be automagically relation!');
				$fooSchema = $this->schemaManager->load(FooSchema::class);
				$barSchema = $this->schemaManager->load(BarSchema::class);
			}
		}
