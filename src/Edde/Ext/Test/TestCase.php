<?php
	declare(strict_types=1);

	namespace Edde\Ext\Test;

	use Edde\Api\Container\ILazyInject;
	use Edde\Common\Container\LazyTrait;
	use Edde\Ext\Container\ContainerFactory;

	class TestCase extends \PHPUnit\Framework\TestCase implements ILazyInject {
		use LazyTrait;

		protected function setUp() {
			ContainerFactory::autowire($this);
		}
	}
