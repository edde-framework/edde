<?php
	declare(strict_types=1);
	namespace Edde\Factory;

	use Edde\Container\ContainerException;
	use Edde\EddeException;
	use Edde\TestCase;

	class ExceptionFactoryTest extends TestCase {
		/**
		 * @throws FactoryException
		 */
		public function testGetReflection() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('kaboom');
			$factory = new ExceptionFactory('boom', EddeException::class, 'kaboom');
			$factory->getReflection($this->container, 'foo');
		}

		/**
		 * @throws ContainerException
		 */
		public function testFactory() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('kaboom');
			$factory = new ExceptionFactory('boom', EddeException::class, 'kaboom');
			$factory->factory($this->container, [], new Reflection(), 'boom');
		}
	}
