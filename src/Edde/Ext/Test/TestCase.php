<?php
	declare(strict_types=1);

	namespace Edde\Ext\Test;

	use Edde\Api\Container\IAutowire;
	use Edde\Common\Container\AutowireTrait;
	use Edde\Ext\Container\ContainerFactory;
	use PHPUnit\Framework\TestCase as PhpUnitTestCase;

	class TestCase extends PhpUnitTestCase implements IAutowire {
		use AutowireTrait;

		protected function setUp() {
			ContainerFactory::inject($this);
		}
	}
