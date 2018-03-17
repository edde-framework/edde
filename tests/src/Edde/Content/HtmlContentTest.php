<?php
	declare(strict_types=1);
	namespace Edde\Content;

	use Edde\TestCase;
	use function iterator_to_array;

	class HtmlContentTest extends TestCase {
		public function testStaticHtmlContent() {
			$htmlContent = new HtmlContent('foo');
			self::assertEquals('text/html', $htmlContent->getType());
			self::assertSame('foo', $htmlContent->getContent());
			self::assertSame(['foo'], iterator_to_array($htmlContent));
		}

		public function testHtmlContentGenerator() {
			$htmlContent = new HtmlContent($generator = (function () {
				yield 'a';
				yield 'b';
				yield 'c';
			})());
			self::assertEquals('text/html', $htmlContent->getType());
			self::assertSame($generator, $htmlContent->getContent());
			self::assertSame(['a', 'b', 'c'], iterator_to_array($htmlContent));
		}
	}
