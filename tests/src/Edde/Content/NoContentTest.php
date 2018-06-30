<?php
	declare(strict_types=1);
	namespace Edde\Content;

	use Edde\TestCase;
	use function iterator_to_array;

	class NoContentTest extends TestCase {
		public function testNoContent() {
			$content = new NoContent();
			self::assertEmpty($content->getContent());
			self::assertSame('text/plain', $content->getType());
			self::assertEmpty(iterator_to_array($content));
		}
	}
