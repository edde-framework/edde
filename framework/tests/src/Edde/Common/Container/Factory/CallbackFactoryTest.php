<?php
	declare(strict_types=1);
	namespace Edde\Common\Container\Factory;

	use Edde\Common\Container\Container;
	use Edde\Common\Container\Parameter;
	use Edde\Common\Container\Reflection;
	use Edde\Ext\Test\TestCase;
	use Edde\Test\FooObject;

	require_once __DIR__ . '/../../assets/assets.php';

	class CallbackFactoryTest extends TestCase {
		public function testFactoryReflection() {
			$factory = new CallbackFactory('\Edde\Test\foo');
			$reflection = $factory->getReflection($container = new Container(), 'foo');
			self::assertEquals(new Reflection([
				new Parameter('fooObject', false, FooObject::class),
			]), $reflection);
			self::assertTrue($factory->canHandle($container, FooObject::class));
		}
	}
