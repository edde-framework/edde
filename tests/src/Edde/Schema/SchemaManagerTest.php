<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Service\Schema\SchemaManager;
	use Edde\TestCase;
	use Edde\User\UserSchema;
	use ProjectMemberSchema;
	use ProjectSchema;
	use function array_values;

	class SchemaManagerTest extends TestCase {
		use SchemaManager;

		/**
		 * @throws SchemaException
		 */
		public function testRelationSchema() {
			$projectMemberSchema = $this->schemaManager->load(ProjectMemberSchema::class);
			$projectSchema = $this->schemaManager->load(ProjectSchema::class);
			$userSchema = $this->schemaManager->load(UserSchema::class);
			self::assertSame($projectMemberSchema, $this->schemaManager->load('foo-bar'));
			self::assertTrue($projectMemberSchema->isRelation(), 'relation schema... is not a relation schema!');
			/**
			 * links test
			 */
			self::assertCount(1, $links = $projectMemberSchema->getLinks(UserSchema::class));
			[$link] = $links;
			self::assertSame(ProjectMemberSchema::class, ($from = $link->getFrom())->getName());
			self::assertSame(ProjectSchema::class, ($to = $link->getTo())->getName());
			self::assertSame('project', $from->getPropertyName());
			self::assertSame('uuid', $to->getPropertyName());
			self::assertCount(1, $links = $projectMemberSchema->getLinks(UserSchema::class));
			[$link] = $links;
			self::assertSame(ProjectMemberSchema::class, ($from = $link->getFrom())->getName());
			self::assertSame(UserSchema::class, ($to = $link->getTo())->getName());
			self::assertSame('user', $from->getPropertyName());
			self::assertSame('uuid', $to->getPropertyName());
			/**
			 * one way relation test
			 */
			self::assertCount(1, $relations = $projectSchema->getRelations(BarSchema::class));
			/** @var $relation IRelation */
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
			self::assertCount(1, $relations = $userSchema->getRelations(FooSchema::class));
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

		/**
		 * @throws SchemaException
		 */
		public function testGetRelationUnknownException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Requested relation schema [nope] does not exists between [FooSchema] and [BarSchema].');
			$this->schemaManager->load(FooSchema::class);
			$this->schemaManager->load(BarSchema::class);
			$this->schemaManager->load(FooBarSchema::class);
			$sourceSchema = $this->schemaManager->load(FooSchema::class);
			$sourceSchema->getRelation(BarSchema::class, 'nope');
		}

		/**
		 * @throws SchemaException
		 */
		public function testGetRelation() {
			$this->schemaManager->load(BarSchema::class);
			$this->schemaManager->load(FooBarSchema::class);
			$sourceSchema = $this->schemaManager->load(FooSchema::class);
			$relation = $sourceSchema->getRelation(BarSchema::class, FooBarSchema::class);
			self::assertSame(FooBarSchema::class, $relation->getSchema()->getName());
		}
	}
