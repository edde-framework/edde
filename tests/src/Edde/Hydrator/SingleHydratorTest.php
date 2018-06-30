<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	use Edde\TestCase;

	class SingleHydratorTest extends TestCase {
		public function testHydrate() {
			$hydrator = new SingleHydrator();
			self::assertEquals('foo', $hydrator->hydrate(['some-name' => 'foo']));
		}

		public function testInput() {
			$hydrator = new SingleHydrator();
			$source = ['a' => true];
			self::assertSame($source, $hydrator->input('nane', $source));
		}

		public function testUpdate() {
			$hydrator = new SingleHydrator();
			$source = ['a' => true];
			self::assertSame($source, $hydrator->update('nane', $source));
		}

		public function testOutput() {
			$hydrator = new SingleHydrator();
			$source = ['a' => true];
			self::assertSame($source, $hydrator->output('nane', $source));
		}
	}
