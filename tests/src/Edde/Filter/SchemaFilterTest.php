<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use DateTime;
	use Edde\Container\ContainerException;
	use Edde\Schema\SchemaException;
	use Edde\Service\Schema\SchemaManager;
	use Edde\TestCase;
	use ProjectSchema;
	use ReflectionException;
	use function is_string;

	class SchemaFilterTest extends TestCase {
		use \Edde\Service\Schema\SchemaFilterService;
		use SchemaManager;

		/**
		 * @throws FilterException
		 * @throws SchemaException
		 */
		public function testSchemaFilter() {
			$schema = $this->schemaManager->getSchema(ProjectSchema::class);
			$object = $this->schemaFilter->input($schema, (object)[
				'start' => '2012-12-20',
			], 'storage');
			self::assertTrue(is_string($object->start));
			self::assertTrue(is_string($object->created));
			self::assertNotEmpty($object->uuid);
			$object = $this->schemaFilter->output($schema, $object, 'storage');
			self::assertInstanceOf(DateTime::class, $object->start);
			self::assertInstanceOf(DateTime::class, $object->created);
		}

		/**
		 * @throws ContainerException
		 * @throws SchemaException
		 * @throws ReflectionException
		 */
		protected function setUp() {
			parent::setUp();
			$this->schemaManager->load(ProjectSchema::class);
		}
	}
