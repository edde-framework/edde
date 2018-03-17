<?php
	declare(strict_types=1);
	namespace Edde\Content;

	use Edde\TestCase;
	use function iterator_to_array;

	class GeneratorContentTest extends TestCase {
		public function testGeneratorContent() {
			$content = new GeneratorContent($func = function () {
				yield 'foo';
				yield 'bar';
				yield 'prd';
			}, 'generator/array');
			self::assertEquals('generator/array', $content->getType());
			self::assertSame($func, $content->getContent());
			self::assertEquals([
				'foo',
				'bar',
				'prd',
			], iterator_to_array($content));
		}
	}
