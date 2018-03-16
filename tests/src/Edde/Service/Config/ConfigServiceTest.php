<?php
	declare(strict_types=1);
	namespace Edde\Service\Config;

	use Edde\Exception\Config\RequiredConfigException;
	use Edde\Exception\Config\RequiredSectionException;
	use Edde\Exception\Config\RequiredValueException;
	use Edde\Inject\Config\ConfigLoader;
	use Edde\Inject\Config\ConfigService;
	use Edde\TestCase;

	class ConfigServiceTest extends TestCase {
		use ConfigService;
		use ConfigLoader;

		/**
		 * @throws RequiredConfigException
		 * @throws RequiredSectionException
		 */
		public function testConfigException() {
			$this->expectException(RequiredSectionException::class);
			$this->expectExceptionMessage('Requested section [boo] is not available!');
			$this->configLoader
				->require(__DIR__ . '/assets/config.ini')
				->optional(__DIR__ . '/assets/config.local.ini')
				->optional(__DIR__ . '/assets/config.nope.ini');
			$this->configService->require('boo');
		}

		/**
		 * @throws RequiredConfigException
		 */
		public function testRequiredConfigException() {
			$this->expectException(RequiredConfigException::class);
			$this->expectExceptionMessage('Required config file [nope] is not available!');
			$this->configLoader->require('nope');
			$this->configLoader->compile();
		}

		/**
		 * @throws RequiredConfigException
		 * @throws RequiredSectionException
		 */
		public function testSection() {
			$this->configLoader->clear();
			$this->configLoader->require(__DIR__ . '/assets/config.ini');
			self::assertEquals(
				(object)[
					'value'   => 'yep',
					'another' => true,
				],
				$this->configService->require('foo')->toObject()
			);
			self::assertEquals(
				(object)[
					'integer' => 42,
					'float'   => 3.14,
				],
				$this->configService->require('bar')->toObject()
			);
		}

		/**
		 * @throws RequiredConfigException
		 */
		public function testCompileOptional() {
			$this->configLoader->require(__DIR__ . '/assets/config.ini');
			$section = $this->configService->optional('moo');
			self::assertEquals('default', $section->optional('item', 'default'));
		}

		/**
		 * @throws RequiredConfigException
		 * @throws RequiredSectionException
		 */
		public function testRequiredSection() {
			$this->expectException(RequiredSectionException::class);
			$this->expectExceptionMessage('Requested section [nope] is not available!');
			$this->configLoader->require(__DIR__ . '/assets/config.ini');
			$this->configService->require('nope');
		}

		/**
		 * @throws RequiredConfigException
		 * @throws RequiredValueException
		 */
		public function testRequiredValue() {
			$this->expectException(RequiredValueException::class);
			$this->expectExceptionMessage('Required section value [foo::nope] is not available!');
			$this->configLoader->require(__DIR__ . '/assets/config.ini');
			$section = $this->configService->optional('foo');
			$section->require('nope');
		}

		/**
		 * @throws RequiredConfigException
		 * @throws RequiredValueException
		 */
		public function testYep() {
			$this->configLoader->require(__DIR__ . '/assets/config.ini');
			$section = $this->configService->optional('foo');
			self::assertTrue($section->require('another') === true);
			$section = $this->configService->optional('bar');
			self::assertEquals(3.14, $section->require('float'));
		}
	}
