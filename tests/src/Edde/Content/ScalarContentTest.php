<?php
	declare(strict_types=1);
	namespace Edde\Content;

	use Edde\TestCase;
	use function iterator_to_array;

	class ScalarContentTest extends TestCase {
		public function testTextContent() {
			$content = new ScalarContent(1);
			self::assertEquals('scalar', $content->getType());
			self::assertEquals(1, $content->getContent());
			self::assertEquals([1], iterator_to_array($content));
		}
	}
