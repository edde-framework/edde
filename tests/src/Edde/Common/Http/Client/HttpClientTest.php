<?php
	declare(strict_types=1);

	namespace Edde\Common\Http\Client;

	use Edde\Api\Http\Client\LazyHttpClientTrait;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Test\TestCase;

	class HttpClientTest extends TestCase {
		use LazyHttpClientTrait;

		public function testSimpleGet() {
			$content = $this->httpClient->get('https://httpbin.org/get')->header('Accept', 'application/json')->execute()->convert([\stdClass::class]);
			self::assertEquals(\stdClass::class, $content->getMime());
			$content = $content->getContent();
			$content->origin = '';
			self::assertEquals((object)[
				'args'    => (object)[],
				'headers' => (object)[
					'Accept'          => 'application/json',
					'Accept-Encoding' => 'utf-8',
					'Connection'      => 'close',
					'Host'            => 'httpbin.org',
				],
				'origin'  => '',
				'url'     => 'https://httpbin.org/get',
			], $content);
		}

		protected function setUp() {
			ContainerFactory::autowire($this);
		}
	}
