<?php
	declare(strict_types = 1);

	namespace Edde\Common\Callback;

	use phpunit\framework\TestCase;

	class CallbackTest extends TestCase {
		public function testCommon() {
			$callback = new Callback($func = function (int $a, int $b) {
				return $a + $b;
			});
			self::assertSame($func, $callback->getCallback());
			self::assertCount(2, $callback->getParameterList());
			$parameterList = [];
			foreach ($callback->getParameterList() as $parameter) {
				$parameterList[] = $parameter->getName();
			}
			self::assertEquals([
				'a',
				'b',
			], $parameterList);
			self::assertEquals(3, $callback(1, 2));
		}
	}
