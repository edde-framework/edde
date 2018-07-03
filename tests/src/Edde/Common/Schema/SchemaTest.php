<?php
	declare(strict_types = 1);

	namespace Edde\Common\Schema;

	use Edde\Api\Schema\SchemaException;
	use phpunit\framework\TestCase;

	class SchemaTest extends TestCase {
		public function testCommon() {
			$schema = new Schema(self::class);
			self::assertSame('SchemaTest', $schema->getName());
			self::assertSame(__NAMESPACE__, $schema->getNamespace());
			self::assertSame(self::class, $schema->getSchemaName());
			self::assertFalse($schema->isUsed());
			self::assertEmpty($schema->getPropertyList());
			self::assertTrue($schema->isUsed());
		}

		public function testCommonProperty() {
			$schema = new Schema(self::class);
			$schema->addProperty((new SchemaProperty($schema, 'guid'))->required()
				->unique()
				->identifier());
			$schema->addProperty((new SchemaProperty($schema, 'name')));
			self::assertCount(2, $schema->getPropertyList());
		}

		public function testLinks() {
			$alphaSchema = new Schema(self::class);
			$alphaSchema->addProperty($guidProperty = new SchemaProperty($alphaSchema, 'guid'));
			$alphaSchema->addProperty($parentProperty = new SchemaProperty($alphaSchema, 'parent'));

			$betaSchema = new Schema(TestCase::class);
			$betaSchema->addProperty($referenceProperty = new SchemaProperty($betaSchema, 'reference'));

			$betaSchema->link('reference', $referenceProperty, $guidProperty);
			$alphaSchema->link('parent', $parentProperty, $guidProperty);

			self::assertTrue($alphaSchema->hasLink('parent'));
			self::assertTrue($betaSchema->hasLink('reference'));

			$parentLink = $alphaSchema->getLink('parent');
			self::assertSame($parentProperty, $parentLink->getSource());
			self::assertSame($guidProperty, $parentLink->getTarget());

			$referenceLink = $betaSchema->getLink('reference');
			self::assertSame($referenceProperty, $referenceLink->getSource());
			self::assertSame($guidProperty, $referenceLink->getTarget());

			self::assertCount(1, $alphaSchema->getLinkList());
			self::assertCount(1, $betaSchema->getLinkList());
		}

		public function testCollections() {
			$alphaSchema = new Schema(self::class);
			$alphaSchema->addProperty($guidProperty = new SchemaProperty($alphaSchema, 'guid'));

			$betaSchema = new Schema(TestCase::class);
			$betaSchema->addProperty($referenceProperty = new SchemaProperty($betaSchema, 'reference'));

			$alphaSchema->collection('referenceList', $guidProperty, $referenceProperty);

			self::assertTrue($alphaSchema->hasCollection('referenceList'));
			$referenceCollection = $alphaSchema->getCollection('referenceList');
			self::assertSame($guidProperty, $referenceCollection->getSource());
			self::assertSame($referenceProperty, $referenceCollection->getTarget());

			self::assertCount(1, $alphaSchema->getCollectionList());
		}

		public function testPropertyException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Property with name [guid] already exists in schema [Edde\Common\Schema\SchemaTest].');
			$schema = new Schema(self::class);
			$schema->addProperty(new SchemaProperty($schema, 'guid'));
			$schema->addProperty(new SchemaProperty($schema, 'guid'));
		}

		public function testLinkException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Schema [Edde\Common\Schema\SchemaTest] already contains link named [parent].');
			$schema = new Schema(self::class);
			$schema->addProperty($guid = new SchemaProperty($schema, 'guid'));
			$schema->addProperty($parent = new SchemaProperty($schema, 'parent'));
			$schema->link('parent', $guid, $parent);
			$schema->link('parent', $guid, $parent);
		}

		public function testCollectionException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Schema [Edde\Common\Schema\SchemaTest] already has collection named [parent].');
			$schema = new Schema(self::class);
			$schema->addProperty($guid = new SchemaProperty($schema, 'guid'));
			$schema->addProperty($parent = new SchemaProperty($schema, 'parent'));
			$schema->collection('parent', $guid, $parent);
			$schema->collection('parent', $guid, $parent);
		}
	}
