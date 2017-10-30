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
				$schema = $this->schemaManager->load(FooBarSchema::class);
				self::assertTrue($schema->isRelation(), 'foo bar schema should be automagicall relation!');
				$link = $schema->getLink('foo');
				self::assertSame('guid', $link->getTarget());
				self::assertSame('foo', $link->getProperty());
				self::assertSame(FooSchema::class, $link->getSchema());
				$link = $schema->getLink('bar');
				self::assertSame('guid', $link->getTarget());
				self::assertSame('bar', $link->getProperty());
				self::assertSame(BarSchema::class, $link->getSchema());
			}
		}
