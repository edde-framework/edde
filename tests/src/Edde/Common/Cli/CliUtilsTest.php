<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cli;

	use phpunit\framework\TestCase;

	class CliUtilsTest extends TestCase {
		public function testCommon() {
			self::assertEquals([
				'foo' => 'argument',
				'arg',
				'bla',
				's' => true,
				'e' => true,
				't' => true,
				'with-assigment' => '1',
				'b' => '2',
			], CliUtils::getArgumentList([
				'--foo',
				'argument',
				'arg',
				'bla',
				'-set',
				'--with-assigment=1',
				'-b=2',
			]));
		}
	}
