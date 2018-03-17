<?php
	declare(strict_types=1);
	namespace Edde\Content;

	use Edde\TestCase;

	class ContentTest extends TestCase {
		public function testContent() {
			$content = new Content('boo', 'content/bla');
			self::assertEquals('content/bla', $content->getType());
			self::assertSame('boo', $content->getContent());
			self::assertEquals(['boo'], iterator_to_array($content));
		}
	}
