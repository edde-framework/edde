<?php
	declare(strict_types=1);
	namespace Edde\Response;

	use Edde\Content\GeneratorContent;
	use Edde\TestCase;
	use function ob_get_clean;
	use function ob_start;

	class ResponseTest extends TestCase {
		public function testResponse() {
			ob_start();
			$response = new Response(new GeneratorContent(function () {
				yield '1';
				yield '2';
				yield '3';
			}, '123'));
			$response->execute();
			self::assertSame('123', ob_get_clean());
		}
	}
