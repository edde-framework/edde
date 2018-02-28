<?php
	namespace Edde\Common\Schema;

	use Edde\Api\Schema\Exception\UnknownSchemaException;
	use Edde\Api\Schema\Inject\SchemaManager;
	use Edde\Ext\Test\TestCase;
	use function array_values;

	class SchemaManagerTest extends TestCase {
		use SchemaManager;

		/**
		 * @throws UnknownSchemaException
		 */
		public function testRelationSchema() {
			$fooBarSchema = $this->schemaManager->load(FooBarSchema::class);
			$fooSchema = $this->schemaManager->load(FooSchema::class);
			$barSchema = $this->schemaManager->load(BarSchema::class);
			self::assertSame($fooBarSchema, $this->schemaManager->load('foo-bar'));
			self::assertTrue($fooBarSchema->isRelation(), 'relation schema... is not a relation schema!');
			/**
			 * links test
			 */
			self::assertCount(1, $links = $fooBarSchema->getLinks(FooSchema::class));
			[$link] = $links;
			self::assertSame(FooBarSchema::class, ($from = $link->getFrom())->getName());
			self::assertSame(FooSchema::class, ($to = $link->getTo())->getName());
			self::assertSame('foo', $from->getPropertyName());
			self::assertSame('uuid', $to->getPropertyName());
			self::assertCount(1, $links = $fooBarSchema->getLinks(BarSchema::class));
			[$link] = $links;
			self::assertSame(FooBarSchema::class, ($from = $link->getFrom())->getName());
			self::assertSame(BarSchema::class, ($to = $link->getTo())->getName());
			self::assertSame('bar', $from->getPropertyName());
			self::assertSame('uuid', $to->getPropertyName());
			/**
			 * one way relation test
			 */
			self::assertCount(1, $relations = $fooSchema->getRelations(BarSchema::class));
			[$relation] = array_values($relations);
			self::assertSame(FooSchema::class, ($fromLink = $relation->getFrom())->getFrom()->getName());
			self::assertSame('uuid', $fromLink->getFrom()->getPropertyName());
			self::assertSame(FooBarSchema::class, $fromLink->getTo()->getName());
			self::assertSame('foo', $fromLink->getTo()->getPropertyName());
			self::assertSame(FooBarSchema::class, ($toLink = $relation->getTo())->getFrom()->getName());
			self::assertSame('bar', $toLink->getFrom()->getPropertyName());
			self::assertSame(BarSchema::class, $toLink->getTo()->getName());
			self::assertSame('uuid', $toLink->getTo()->getPropertyName());
			/**
			 * reverse way relation test
			 */
			self::assertCount(1, $relations = $barSchema->getRelations(FooSchema::class));
			[$relation] = array_values($relations);
			self::assertSame(BarSchema::class, ($fromLink = $relation->getFrom())->getFrom()->getName());
			self::assertSame('uuid', $fromLink->getFrom()->getPropertyName());
			self::assertSame(FooBarSchema::class, $fromLink->getTo()->getName());
			self::assertSame('bar', $fromLink->getTo()->getPropertyName());
			self::assertSame(FooBarSchema::class, ($toLink = $relation->getTo())->getFrom()->getName());
			self::assertSame('foo', $toLink->getFrom()->getPropertyName());
			self::assertSame(FooSchema::class, $toLink->getTo()->getName());
			self::assertSame('uuid', $toLink->getTo()->getPropertyName());
		}
	}
