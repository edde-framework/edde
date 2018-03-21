<?php
	declare(strict_types=1);
	namespace Edde\Utils;

	use Edde\Service\Utils\StringUtils;
	use Edde\TestCase;

	class StringUtilsTest extends TestCase {
		use StringUtils;

		public function testMatch() {
			self::assertSame([
				'key'   => 'foo',
				'value' => 'bar',
			], $this->stringUtils->match('foo=bar', '~(?<key>[a-z]+)=(?<value>[a-z]+)~', true));
		}

		public function testCapitalize() {
			self::assertSame('Foo Bar', $this->stringUtils->capitalize('foo bar'));
		}

		public function testFromCamelCase() {
			self::assertEquals([
				'Foo',
				'Bar',
			], $this->stringUtils->fromCamelCase('FooBar'));
			self::assertEquals([
				'Bar',
			], $this->stringUtils->fromCamelCase('FooBar', 1));
		}

		public function testRecamel() {
			self::assertEquals('foo-bar', $this->stringUtils->recamel('FooBar'));
			self::assertEquals('bar#prd', $this->stringUtils->recamel('FooBarPrd', '#', 1));
		}

		public function testExtract() {
			self::assertEquals('StringUtilsTest', $this->stringUtils->extract(self::class));
			self::assertEquals('Utils', $this->stringUtils->extract(self::class, '\\', -2));
		}

		public function testNormalize() {
			self::assertEquals("Bl\na", $this->stringUtils->normalize("Bl\ra"));
		}
	}
