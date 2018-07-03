<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use phpunit\framework\TestCase;

	class HttpMessageTest extends TestCase {
		public function testEmptyMessage() {
			$message = new HttpMessage('', '');
			self::assertFalse($message->isUsed(), 'Message has been used!');
			self::assertEquals('application/octet-stream', $message->getContentType('application/octet-stream'));
			self::assertTrue($message->isUsed(), 'Message has NOT been used!');
		}

		public function testSimpleMessage() {
			$message = new HttpMessage('here is plain text', 'Content-Type: text/plain');
			self::assertEquals('text/plain', $message->getContentType());
		}

		public function testComplexMessage() {
			$message = new HttpMessage(file_get_contents(__DIR__ . '/assets/complex-message'), 'Content-Type: Multipart/Related; boundary="==Ia972IE5ZAauQb99oe4cHtcKVOPm+TnhmgRNG0ettImxcp0myeUedUROkV00=="; type="application/xop+xml"; start="<d569a93d-2406-4130-ba60-a61cb17f2818@uuid>"; start-info="application/soap+xml"');
			self::assertEquals('multipart/related', $message->getContentType());
			self::assertEquals([
				'boundary' => '==Ia972IE5ZAauQb99oe4cHtcKVOPm+TnhmgRNG0ettImxcp0myeUedUROkV00==',
				'type' => 'application/xop+xml',
				'start' => '<d569a93d-2406-4130-ba60-a61cb17f2818@uuid>',
				'start-info' => 'application/soap+xml',
			], $message->getHeaderList()
				->getContentType()
				->getParameterList());
			self::assertEquals([
				'd569a93d-2406-4130-ba60-a61cb17f2818@uuid',
				'01798d1a-28c8-4ee9-ba5f-eb577a2d0ce7@uuid',
			], $message->getContentList());
		}
	}
