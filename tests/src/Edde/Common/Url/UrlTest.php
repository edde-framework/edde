<?php
	declare(strict_types = 1);

	namespace Edde\Common\Url;

	use Edde\Api\Url\UrlException;
	use Edde\Common\File\FileUtils;
	use phpunit\framework\TestCase;

	class UrlTest extends TestCase {
		public function testCommon() {
			$url = Url::create();
			$url->setHost('edde-framework.org');
			$url->setPort(8182);
			$url->setScheme('https');
			self::assertSame($expected = 'https://edde-framework.org:8182/', (string)$url);
			self::assertSame($expected, $url->getAbsoluteUrl());
		}

		public function testParameterList() {
			$url = Url::create();
			$url->setHost('edde-framework.org');
			$url->setPort(8182);
			$url->setScheme('https');
			$url->setQuery([
				'foo' => 'foo-value',
				'bar' => 'bar-value',
			]);
			self::assertSame($expected = 'https://edde-framework.org:8182/?foo=foo-value&bar=bar-value', (string)$url);
			self::assertSame($expected, $url->getAbsoluteUrl());
		}

		public function testUrlException() {
			$this->expectException(UrlException::class);
			$this->expectExceptionMessage('Malformed URL [ftp://picovina:http//].');
			Url::create('ftp://picovina:http//');
		}

		public function testUrlParser() {
			$url = Url::create('https://edde-framework.org:8182?foo=foo-value&bar=bar-value');
			self::assertEquals('https', $url->getScheme());
			self::assertEquals('edde-framework.org', $url->getHost());
			self::assertEquals(8182, $url->getPort());
			self::assertEquals([
				'foo' => 'foo-value',
				'bar' => 'bar-value',
			], $url->getQuery());
		}

		public function testExtended() {
			$url = Url::create('https://edde-framework.org:8182/foo/index.html?foo=foo-value&bar=bar-value');
			self::assertEquals('https', $url->getScheme());
			self::assertEquals('edde-framework.org', $url->getHost());
			self::assertEquals('/foo/index.html', $url->getPath());
			self::assertEquals('index.html', $url->getResourceName());
			self::assertEquals('html', $url->getExtension());
			self::assertEquals(8182, $url->getPort());
			self::assertEquals([
				'foo' => 'foo-value',
				'bar' => 'bar-value',
			], $url->getQuery());
		}

		public function testMissingHost() {
			$url = FileUtils::url($file = 'c:\\windows\\file.txt');
			self::assertSame('file:///' . FileUtils::normalize($file), $url->getAbsoluteUrl());
			self::assertEquals('file', $url->getScheme());
			self::assertEmpty($url->getHost());
			self::assertEquals('c:/windows/file.txt', $url->getPath());
			self::assertEquals('file.txt', $url->getResourceName());
			self::assertEquals('txt', $url->getExtension());
		}

		public function testLinuxPath() {
			$url = FileUtils::url('/etc/some/hidden/config.conf');
			self::assertEquals('file', $url->getScheme());
			self::assertEmpty($url->getHost());
			self::assertEquals('/etc/some/hidden/config.conf', $url->getPath());
			self::assertEquals('config.conf', $url->getResourceName());
			self::assertEquals('conf', $url->getExtension());
		}

		public function testLinuxPath2() {
			$url = Url::create($file = 'file:///etc/some/hidden/config.conf');
			self::assertEquals($file, $url->getAbsoluteUrl());
			self::assertEquals('file', $url->getScheme());
			self::assertEmpty($url->getHost());
			self::assertEquals('/etc/some/hidden/config.conf', $url->getPath());
			self::assertEquals('config.conf', $url->getResourceName());
			self::assertEquals('conf', $url->getExtension());
		}
	}
