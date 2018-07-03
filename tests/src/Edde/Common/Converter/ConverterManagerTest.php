<?php
	declare(strict_types=1);

	namespace Edde\Common\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\File\IRootDirectory;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\File\RootDirectory;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Test\TestCase;

	class ConverterManagerTest extends TestCase {
		use LazyConverterManagerTrait;

		public function testTargetArray() {
			$content = $this->converterManager->convert($source = ['foo'], 'array', [
				'string',
				'text/plain',
				'json',
			])->convert();
			self::assertEquals(json_encode($source), $content->getContent());
			self::assertEquals('application/json', $content->getMime());
		}

		public function testKaboom() {
			$this->expectException(ConverterException::class);
			$this->expectExceptionMessage('Cannot convert unknown/unsupported source mime [array] to any of [string, text/plain].');
			self::assertEquals(json_encode($source = ['foo']), $this->converterManager->convert($source, 'array', [
				'string',
				'text/plain',
			])->convert());
		}

		protected function setUp() {
			ContainerFactory::autowire($this, [
				IRootDirectory::class => ContainerFactory::instance(RootDirectory::class, [__DIR__]),
				new ClassFactory(),
			]);
		}
	}
