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
				self::assertSame($fooBarSchema, $this->schemaManager->load('foo-bar'));
				self::assertTrue($fooBarSchema->isRelation(), 'relation schema... is not a relation schema!');
				self::assertCount(1, $linkList = $fooBarSchema->getLinks(FooSchema::class));
				list($link) = $linkList;
				self::assertSame(FooBarSchema::class, ($from = $link->getFrom())->getName());
				self::assertSame(FooSchema::class, ($to = $link->getTo())->getName());
				self::assertSame('foo', $from->getPropertyName());
				self::assertSame('guid', $to->getPropertyName());
				$fooSchema = $this->schemaManager->load(FooSchema::class);
				self::assertCount(1, $linkList = $fooSchema->getLinkToList(FooBarSchema::class));
				list($link) = $linkList;
				self::assertSame(FooBarSchema::class, ($from = $link->getFrom())->getName());
				self::assertSame(FooSchema::class, ($to = $link->getTo())->getName());
				self::assertSame('foo', $from->getPropertyName());
				self::assertSame('guid', $to->getPropertyName());
				$barSchema = $this->schemaManager->load(BarSchema::class);
				self::assertCount(1, $linkList = $barSchema->getLinkToList(FooBarSchema::class));
				list($link) = $linkList;
				self::assertSame(FooBarSchema::class, ($from = $link->getFrom())->getName());
				self::assertSame(FooSchema::class, ($to = $link->getTo())->getName());
				self::assertSame('bar', $link->getSourceProperty()->getName());
				self::assertSame('guid', $link->getTargetProperty()->getName());
				/**
				 * foo test
				 */
				self::assertNotEmpty($relationList = $fooSchema->getRelationList(BarSchema::class));
				self::assertCount(1, $relationList);
				list($relation) = $relationList;
				self::assertSame(FooBarSchema::class, $relation->getSchema()->getName());
				self::assertSame(BarSchema::class, $relation->getTargetLink()->getTargetSchema()->getName());
				self::assertSame('guid', $relation->getTargetLink()->getTargetProperty()->getName());
				self::assertSame('foo', $relation->getSourceLink()->getSourceProperty()->getName());
				/**
				 * bar test
				 */
				self::assertNotEmpty($relationList = $barSchema->getRelationList(FooSchema::class));
				self::assertCount(1, $relationList);
				list($relation) = $relationList;
				self::assertSame(FooBarSchema::class, $relation->getSchema()->getName());
				self::assertSame(FooSchema::class, $relation->getTargetLink()->getTargetSchema()->getName());
				self::assertSame('guid', $relation->getTargetLink()->getTargetProperty()->getName());
				self::assertSame('bar', $relation->getSourceLink()->getSourceProperty()->getName());
			}
		}
