<?php
	declare(strict_types=1);
	namespace Edde\Container\Factory;

	use Edde\Container\Container;
	use Edde\Container\Parameter;
	use Edde\Container\Reflection;
	use Edde\Test\FooObject;
	use Edde\TestCase;
	use ReflectionException;

	class CallbackFactoryTest extends TestCase {
		/**
		 * @throws ReflectionException
		 */
		public function testFactoryReflection() {
			$factory = new CallbackFactory('\Edde\Test\foo');
			$reflection = $factory->getReflection($container = new Container(), 'foo');
			self::assertEquals(new Reflection([
				new Parameter('fooObject', false, FooObject::class),
			]), $reflection);
			self::assertTrue($factory->canHandle($container, FooObject::class));
		}
	}
