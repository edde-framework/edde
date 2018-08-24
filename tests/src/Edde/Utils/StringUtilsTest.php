<?php
	declare(strict_types=1);
	namespace Edde\Utils;

	use Edde\Service\Utils\StringUtils;
	use Edde\TestCase;
	use function is_array;

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

		public function testMatchNope() {
			$match = $this->stringUtils->match('nope', '~[0-9]~', false, $trim = ['a' => 1, 'b' => 2]);
			self::assertEquals($trim, $match);
		}

		public function testMatchTrim() {
			$match = $this->stringUtils->match('nope123', '~[0-9]+~', false, $trim = ['a' => 1, 'b' => 2]);
			$trim[0] = '123';
			self::assertEquals($trim, $match);
		}

		public function testMatchAll() {
			$string = '
/**
 * @help run an upgrade to the given version or do full upgrade to the latest available version
 * @help [--version] <version name>: select target upgrade
 */
			';
			$result = $this->stringUtils->matchAll($string, '~@help\s+(?<help>.*)~', true);
			self::assertEquals([
				'help' => [
					'run an upgrade to the given version or do full upgrade to the latest available version',
					'[--version] <version name>: select target upgrade',
				],
			], $result);
		}

		public function testMatchAll2() {
			$string = '
/**
 * @help run an upgrade to the given version or do full upgrade to the latest available version
 * @help [--version] <version name>: select target upgrade
 */
			';
			$result = $this->stringUtils->matchAll($string, '~nope~', true);
			self::assertTrue(is_array($result));
			self::assertEmpty($result);
		}

		public function testMatchAllTrim() {
			$string = '
/**
 * @help run an upgrade to the given version or do full upgrade to the latest available version
 * @help [--version] <version name>: select target upgrade
 */
			';
			$result = $this->stringUtils->matchAll($string, '~@help\s+(?<help>.*)~', true, $trim = ['a' => true, 'b' => 'yep']);
			$trim['help'] = [
				'run an upgrade to the given version or do full upgrade to the latest available version',
				'[--version] <version name>: select target upgrade',
			];
			self::assertEquals($trim, $result);
		}

		public function testClassName() {
			self::assertEquals('BlaBla', $this->stringUtils->className('bla-bla'));
			self::assertEquals('Bla\\Bla', $this->stringUtils->className('bla.bla'));
			self::assertEquals('FooBar\\BarFoo\\BlaBla', $this->stringUtils->className('foo-bar.bar-foo.bla-bla'));
		}
	}
