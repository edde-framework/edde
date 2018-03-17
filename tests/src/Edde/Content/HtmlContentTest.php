<?php
	declare(strict_types=1);
	namespace Edde\Content;

	use Edde\TestCase;
	use function iterator_to_array;

	class HtmlContentTest extends TestCase {
		public function testStaticHtmlContent() {
			$content = new HtmlContent('foo');
			self::assertEquals('text/html', $content->getType());
			self::assertSame('foo', $content->getContent());
			self::assertSame(['foo'], iterator_to_array($content));
		}

		public function testHtmlContentGenerator() {
			$content = new HtmlContent($generator = (function () {
				yield 'a';
				yield 'b';
				yield 'c';
			})());
			self::assertEquals('text/html', $content->getType());
			self::assertSame($generator, $content->getContent());
			self::assertSame(['a', 'b', 'c'], iterator_to_array($content));
		}
	}
