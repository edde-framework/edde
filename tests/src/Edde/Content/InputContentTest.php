<?php
	declare(strict_types=1);
	namespace Edde\Content;

	use Edde\TestCase;

	class InputContentTest extends TestCase {
		public function testInputContent() {
			$content = new InputContent('application/json');
			self::assertEmpty($content->getContent());
			self::assertSame('application/json', $content->getType());
			$chunks = [];
			foreach ($content as $chunk) {
				$chunks[] = $chunk;
			}
			self::assertEmpty($chunks);
		}
	}
