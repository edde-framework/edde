<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Container\Factory\InstanceFactory;
	use Edde\Service\Application\Context;
	use Edde\TestCase;

	class ContextTest extends TestCase {
		use Context;

		public function testContext() {
			self::assertEquals(TestContext::class, $this->context->getId());
			self::assertEquals('34376332-3764-4339-a162-393762613531', $this->context->getUuid());
			self::assertEquals([
				0 => 'Foo#Bar',
				1 => 'Bar#Foo',
			], $this->context->cascade('#'));
			self::assertEquals([
				0 => 'Foo#Bar#Cascade',
				1 => 'Bar#Foo#Cascade',
			], $this->context->cascade('#', 'Cascade'));
		}

		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(
				IContext::class,
				TestContext::class
			), IContext::class);
		}
	}
