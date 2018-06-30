<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	use DateTime;
	use Edde\Container\ContainerException;
	use Edde\Filter\FilterException;
	use Edde\Schema\SchemaException;
	use Edde\Service\Schema\SchemaManager;
	use Edde\TestCase;
	use IssueSchema;

	class SchemaHydratorTest extends TestCase {
		use SchemaManager;

		/**
		 * @throws ContainerException
		 * @throws FilterException
		 * @throws SchemaException
		 */
		public function testHydrate() {
			$this->container->inject($hydrator = new SchemaHydrator());
			$source = [
				'a' => true,
				'b' => false,
				'c' => new DateTime(),
			];
			self::assertSame($source, $hydrator->hydrate($source));
		}

		/**
		 * @throws ContainerException
		 * @throws FilterException
		 * @throws SchemaException
		 */
		public function testOutput() {
			$this->schemaManager->load(IssueSchema::class);
			$this->container->inject($hydrator = new SchemaHydrator());
			$source = $hydrator->output(IssueSchema::class, []);
			self::assertEquals([
				'name'   => '',
				'due'    => null,
				'weight' => 1,
				'uuid'   => '',
			], $source);
		}
	}
