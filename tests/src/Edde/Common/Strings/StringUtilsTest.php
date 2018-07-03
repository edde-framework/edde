<?php
	declare(strict_types = 1);

	namespace Edde\Common\Strings;

	use phpunit\framework\TestCase;

	class StringUtilsTest extends TestCase {
		public function testStringUtils() {
			self::assertEquals("foo\nbar", StringUtils::normalize("foo\r\nbar"));
			self::assertEquals('Capital Letters', StringUtils::capitalize('capital letters'));
			self::assertEquals('bar', StringUtils::substring('foo bar', 4));
			self::assertTrue(StringUtils::compare('foo bar', 'foo', 3));
			self::assertTrue(StringUtils::compare('foo bar', 'foo bar'));
			self::assertEquals($unicode = "ěščřžýáíé", StringUtils::checkUnicode($unicode));
			self::assertEquals('@', StringUtils::chr(64));
			self::assertTrue(StringUtils::isEncoding($unicode));
			self::assertEquals($unicode, StringUtils::fixEncoding($unicode));
			self::assertEquals('1.25 kB', StringUtils::toHumanSize(1280, 2));
			self::assertEquals('57.40 MB', StringUtils::toHumanSize(60192293, 2));
			self::assertEquals([
				'Foo',
				'Bar',
			], StringUtils::fromCamelCase('FooBar'));
			self::assertEquals('foo-bar', StringUtils::recamel('FooBar'));
			self::assertEquals('FooBar', StringUtils::toCamelCase('foo-bar'));
			self::assertEquals('fooBar', StringUtils::toCamelHump('foo-bar'));
//			self::assertEquals('escrzyaie', StringUtils::toAscii($unicode));
//			self::assertEquals('some-title-with-escrzyaie', StringUtils::webalize('Some title with ěščřžýáíé'));
			self::assertEquals(['o b'], StringUtils::match('foo bar', '~o\s+b~'));
			self::assertEquals('fo-hovno-ar', StringUtils::replace('foo bar', '~o\s+b~', '-hovno-'));
			self::assertEquals([
				'foo',
				'bar',
			], StringUtils::split('foo-bar', '~-~'));
			self::assertEquals('Foo', StringUtils::firstUpper('foo'));
			self::assertEquals('FOO', StringUtils::upper('foo'));
			self::assertEquals('foF', StringUtils::firstLower('FoF'));
			self::assertEquals('foo', StringUtils::lower('FOO'));
			self::assertEquals([
				'a',
				'b',
				'c',
			], iterator_to_array(StringUtils::createIterator('abc')));
			self::assertNotEquals("\xEF\xBB\xBFstring", StringUtils::checkUnicode("\xEF\xBB\xBFstring"));
			self::assertEquals("string", StringUtils::checkUnicode("\xEF\xBB\xBFstring"));
		}

		public function testExtract() {
			$source = static::class;
			self::assertEquals('StringUtilsTest', StringUtils::extract($source));
			self::assertEquals('Edde', StringUtils::extract($source, '\\', 0));
			self::assertEquals('Common', StringUtils::extract($source, '\\', 1));
		}
	}
