<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\TestCase;

	class RequestTest extends TestCase {
		public function testSimpleRequest() {
			$request = new Request('service', 'method', ['a' => true]);
			self::assertSame('service', $request->getService());
			self::assertSame('method', $request->getMethod());
			self::assertSame(['a' => true], $request->getParams());
		}
	}
