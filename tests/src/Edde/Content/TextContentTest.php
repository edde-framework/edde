<?php
	declare(strict_types=1);
	namespace Edde\Content;

	use Edde\TestCase;
	use function iterator_to_array;

	class TextContentTest extends TestCase {
		public function testTextContent() {
			$content = new TextContent('text');
			self::assertEquals('text/plain', $content->getType());
			self::assertEquals('text', $content->getContent());
			self::assertEquals(['text'], iterator_to_array($content));
		}
	}
