<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Container\ContainerException;
	use Edde\Query\Query;
	use Edde\Schema\SchemaException;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use Edde\TestCase;
	use ProjectMemberSchema;
	use ProjectSchema;
	use ReflectionException;
	use UserSchema;

	class PdoCompilerTest extends TestCase {
		use SchemaManager;
		use Container;

		/**
		 * @throws ContainerException
		 */
		public function testSimpleQuery() {
			$query = new Query();
			$query->selects([
				'u'  => UserSchema::class,
				'p'  => ProjectSchema::class,
				'pm' => ProjectMemberSchema::class,
			]);
			$query->attach('p', 'u', 'pm');
			$query->order('pm', 'user');
			$query->page(2, 10);
			$compiler = $this->container->create(PdoCompiler::class, ['"'], __METHOD__);
			$commands = $compiler->compile($query);
		}

		/**
		 * @throws ContainerException
		 * @throws SchemaException
		 * @throws ReflectionException
		 */
		protected function setUp() {
			parent::setUp();
			$this->schemaManager->loads([
				ProjectSchema::class,
				ProjectMemberSchema::class,
				UserSchema::class,
			]);
		}
	}
