<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Schema\Exception\LinkException;
		use Edde\Api\Schema\Exception\UnknownSchemaException;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Ext\Test\TestCase;

		class SchemaManagerTest extends TestCase {
			use SchemaManager;

			/**
			 * @throws UnknownSchemaException
			 * @throws LinkException
			 */
			public function testRelationSchema() {
				$fooBarSchema = $this->schemaManager->load(FooBarSchema::class);
				self::assertTrue($fooBarSchema->isRelation(), 'foo bar schema should be automagicall relation!');
				$fooSchema = $this->schemaManager->load(FooSchema::class);
//				$link = $fooSchema->getLinkTo(FooBarSchema::class);
//				self::assertSame(FooSchema::class, $link->getSchema());
//				self::assertSame('guid', $link->getTarget());
//				self::assertSame('foo', $link->getSource());
//				$relation = $fooBarSchema->getRelationTo(BarSchema::class, FooBarSchema::class);
			}
		}
