<?php
	declare(strict_types=1);
	namespace Edde\Content;

	use Edde\TestCase;
	use function iterator_to_array;

	class InputContentTest extends TestCase {
		public function testInputContent() {
			$content = new InputContent('application/json');
			self::assertEmpty($content->getContent());
			self::assertSame('application/json', $content->getType());
			self::assertEmpty(iterator_to_array($content));
		}
	}
