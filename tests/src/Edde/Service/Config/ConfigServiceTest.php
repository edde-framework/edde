<?php
	declare(strict_types=1);
	namespace Edde\Service\Config;

	use Edde\Config\ConfigException;
	use Edde\Inject\Config\ConfigLoader;
	use Edde\Inject\Config\ConfigService;
	use Edde\TestCase;

	class ConfigServiceTest extends TestCase {
		use ConfigService;
		use ConfigLoader;

		/**
		 * @throws ConfigException
		 */
		public function testConfigException() {
			$this->expectException(ConfigException::class);
			$this->expectExceptionMessage('Requested section [boo] is not available!');
			$this->configLoader
				->require(__DIR__ . '/assets/config.ini')
				->optional(__DIR__ . '/assets/config.local.ini')
				->optional(__DIR__ . '/assets/config.nope.ini');
			$this->configService->require('boo');
		}

		/**
		 * @throws ConfigException
		 */
		public function testRequiredConfigException() {
			$this->expectException(ConfigException::class);
			$this->expectExceptionMessage('Required config file [nope] is not available!');
			$this->configLoader->require('nope');
			$this->configLoader->compile();
		}

		/**
		 * @throws ConfigException
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
		 * @throws ConfigException
		 */
		public function testCompileOptional() {
			$this->configLoader->require(__DIR__ . '/assets/config.ini');
			$section = $this->configService->optional('moo');
			self::assertEquals('default', $section->optional('item', 'default'));
		}

		/**
		 * @throws ConfigException
		 */
		public function testRequiredSection() {
			$this->expectException(ConfigException::class);
			$this->expectExceptionMessage('Requested section [nope] is not available!');
			$this->configLoader->require(__DIR__ . '/assets/config.ini');
			$this->configService->require('nope');
		}

		/**
		 * @throws ConfigException
		 */
		public function testRequiredValue() {
			$this->expectException(ConfigException::class);
			$this->expectExceptionMessage('Required section value [foo::nope] is not available!');
			$this->configLoader->require(__DIR__ . '/assets/config.ini');
			$section = $this->configService->optional('foo');
			$section->require('nope');
		}

		/**
		 * @throws ConfigException
		 */
		public function testYep() {
			$this->configLoader->require(__DIR__ . '/assets/config.ini');
			$section = $this->configService->optional('foo');
			self::assertTrue($section->require('another') === true);
			$section = $this->configService->optional('bar');
			self::assertEquals(3.14, $section->require('float'));
		}
	}
