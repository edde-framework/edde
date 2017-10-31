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
				self::assertCount(1, $linkList = $fooBarSchema->getLinkList(FooSchema::class));
				list($link) = $linkList;
				self::assertSame(FooBarSchema::class, $link->getSourceSchema()->getName());
				self::assertSame(FooSchema::class, $link->getTargetSchema()->getName());
				self::assertSame('foo', $link->getSourceProperty()->getName());
				self::assertSame('guid', $link->getTargetProperty()->getName());
				self::assertTrue($fooBarSchema->isRelation(), 'foo bar schema should be automagically relation!');
				$fooSchema = $this->schemaManager->load(FooSchema::class);
				self::assertCount(1, $linkList = $fooSchema->getLinkToList(FooBarSchema::class));
				list($link) = $linkList;;
				self::assertSame(FooBarSchema::class, $link->getSourceSchema()->getName());
				self::assertSame(FooSchema::class, $link->getTargetSchema()->getName());
				self::assertSame('foo', $link->getSourceProperty()->getName());
				self::assertSame('guid', $link->getTargetProperty()->getName());
				$barSchema = $this->schemaManager->load(BarSchema::class);
				self::assertCount(1, $linkList = $barSchema->getLinkToList(FooBarSchema::class));
				list($link) = $linkList;
				self::assertSame(FooBarSchema::class, $link->getSourceSchema()->getName());
				self::assertSame(BarSchema::class, $link->getTargetSchema()->getName());
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
