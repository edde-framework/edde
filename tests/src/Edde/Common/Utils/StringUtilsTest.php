<?php
	declare(strict_types=1);
	namespace Edde\Common\Utils;

	use Edde\Inject\Utils\StringUtils;
	use Edde\TestCase;

	class StringUtilsTest extends TestCase {
		use StringUtils;

		public function testMatch() {
			self::assertSame([
				'key'   => 'foo',
				'value' => 'bar',
			], $this->stringUtils->match('foo=bar', '~(?<key>[a-z]+)=(?<value>[a-z]+)~', true));
		}
	}
