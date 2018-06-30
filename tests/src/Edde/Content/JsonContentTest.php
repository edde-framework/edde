<?php
	declare(strict_types=1);
	namespace Edde\Content;

	use Edde\TestCase;
	use function iterator_to_array;
	use function json_encode;

	class JsonContentTest extends TestCase {
		public function testInputContent() {
			$content = new JsonContent($source = json_encode(['a' => true]));
			self::assertSame($source, $content->getContent());
			self::assertSame('application/json', $content->getType());
			self::assertSame([$source], iterator_to_array($content));
		}
	}
