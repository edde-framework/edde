<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Container\ContainerException;
	use Edde\Query\Query;
	use Edde\Query\QueryException;
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
		 * @throws SchemaException
		 * @throws QueryException
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
			$wheres = $query->wheres();
			$wheres->where('project status in')->in('p', 'status');
			$wheres->where('user uuid')->equalTo('u', 'uuid');
			$wheres->where('is owner')->equalTo('pm', 'owner');
			$wheres->where('project name')->equalTo('p', 'name');
			// ... 2. chain them together using operators and groups
			$chains = $wheres->chains();
			$chains->chain('da group')->where('user uuid')->and('is owner')->and('project name');
			$chains->chain()->where('project status in')->or('da group');
			// ... 3. set parameters for where based on given or guessed names
//			$query->params();
			/** @var $compiler ICompiler */
			$compiler = $this->container->create(PdoCompiler::class, ['"'], __METHOD__);
			$commands = $compiler->compile($query, [
				'user uuid'         => 'on',
				'is owner'          => true,
				'project name'      => 'expected project',
				'project status in' => (function () {
					yield from [ProjectSchema::STATUS_CREATED, ProjectSchema::STATUS_STARTED];
				})(),
			]);
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
