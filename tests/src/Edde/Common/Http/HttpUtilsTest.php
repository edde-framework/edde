<?php
	declare(strict_types=1);
	namespace Edde\Common\Utils;

	use Edde\Api\Http\Inject\HttpUtils;
	use Edde\Common\Http\ContentType;
	use Edde\Common\Http\RequestHeader;
	use Edde\Common\Url\Url;
	use Edde\Ext\Test\TestCase;

	class HttpUtilsTest extends TestCase {
		use HttpUtils;

		public function testAccept() {
			self::assertSame(['*/*'], $this->httpUtils->accept());
			self::assertSame([
				'text/html',
				'application/xhtml+xml',
				'image/webp',
				'image/apng',
				'application/xml',
				'*/*',
			], $this->httpUtils->accept('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8'));
		}

		public function testLanguage() {
			self::assertSame(['en'], $this->httpUtils->language());
			self::assertSame([
				'cs',
				'en',
			], $this->httpUtils->language('cs,en;q=0.8'));
		}

		public function testContentType() {
			$contentType = $this->httpUtils->contentType('text/html; charset=utf-8');
			self::assertEquals('text/html', $contentType->getMime());
			self::assertEquals('utf-8', $contentType->getCharset('foo'));
			self::assertEquals([
				'charset' => 'utf-8',
			], $contentType->getParameterList());
		}

		public function testCookie() {
			self::assertEquals([
				'name'     => '_gh_sess',
				'value'    => 'eyJsYXN0MiLCJsabd2a61',
				'path'     => '/',
				'secure'   => true,
				'httpOnly' => true,
				'expire'   => 0,
				'domain'   => null,
			], $this->httpUtils->cookie('_gh_sess=eyJsYXN0MiLCJsabd2a61; path=/; secure; HttpOnly')->toArray());
			self::assertEquals([
				'name'     => '__Host-user_session_same_site',
				'value'    => 'nhTbc44ff4l3Lwxj',
				'path'     => '/',
				'secure'   => true,
				'httpOnly' => true,
				'expire'   => 1507282883,
				'domain'   => null,
			], $this->httpUtils->cookie('__Host-user_session_same_site=nhTbc44ff4l3Lwxj; path=/; expires=Fri, 06 Oct 2017 09:41:23 -0000; secure; HttpOnly')->toArray());
		}

		public function testRequestHeader() {
			$requestHeader = $this->httpUtils->requestHeader($header = 'GET /edde-framework/edde-framework HTTP/1.1');
			self::assertEquals([
				'method'  => 'GET',
				'path'    => '/edde-framework/edde-framework',
				'version' => '1.1',
			], $requestHeader->toArray());
			self::assertSame($header, (string)$requestHeader);
		}

		public function testResponseHeader() {
			$responseHeader = $this->httpUtils->responseHeader($header = 'HTTP/1.1 301 Moved Permanently');
			self::assertEquals([
				'version' => '1.1',
				'code'    => '301',
				'message' => 'Moved Permanently',
			], $responseHeader->toArray());
			self::assertSame($header, (string)$responseHeader);
		}

		public function testHeaders() {
			$headers = str_replace("\n", "\r\n", 'GET /edde-framework/edde-framework HTTP/1.1
Host: github.com
Connection: keep-alive
Cache-Control: max-age=0
Content-Type: text/html; charset=utf-8
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win87; x87) AppleWebKit/527.36 (KHTML, like Gecko) Chrome/14.0.4173.241 Safari/317.35
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8
DNT: 1
Referer: https://github.com/edde-framework/edde-framework
Accept-Encoding: gzip, deflate, br
Accept-Language: cs,en;q=0.8');
			self::assertEquals([
				'http-request'              => new RequestHeader('GET', '/edde-framework/edde-framework', '1.1'),
				'Host'                      => Url::create('github.com'),
				'Connection'                => 'keep-alive',
				'Cache-Control'             => 'max-age=0',
				'Content-Type'              => new ContentType('text/html', ['charset' => 'utf-8']),
				'Upgrade-Insecure-Requests' => '1',
				'User-Agent'                => 'Mozilla/5.0 (Windows NT 10.0; Win87; x87) AppleWebKit/527.36 (KHTML, like Gecko) Chrome/14.0.4173.241 Safari/317.35',
				'Accept'                    => [
					'text/html',
					'application/xhtml+xml',
					'image/webp',
					'image/apng',
					'application/xml',
					'*/*',
				],
				'DNT'                       => '1',
				'Referer'                   => 'https://github.com/edde-framework/edde-framework',
				'Accept-Encoding'           => 'gzip, deflate, br',
				'Accept-Language'           => [
					'cs',
					'en',
				],
			], $this->httpUtils->parseHeaders($headers)->toArray());
		}
	}
