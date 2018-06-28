<?php
	declare(strict_types=1);
	namespace Edde\Configurable;

	use Edde\Edde;
	use Edde\TestCase;

	class ConfigurableTest extends TestCase {
		public function testInit() {
			$sample = new class() extends Edde implements IConfigurable {
				public $init = false;
				public $setup = false;

				protected function handleInit(): void {
					$this->init = true;
				}

				protected function handleSetup(): void {
					$this->setup = true;
				}
			};
			$sample->init();
			self::assertTrue($sample->init);
			self::assertFalse($sample->setup);
		}

		public function testSetup() {
			$sample = new class() extends Edde implements IConfigurable {
				public $init = false;
				public $setup = false;

				protected function handleInit(): void {
					$this->init = true;
				}

				protected function handleSetup(): void {
					$this->setup = true;
				}
			};
			$sample->setup();
			self::assertTrue($sample->init);
			self::assertTrue($sample->setup);
		}

		public function testSetupOverride() {
			$sample = new class() extends Edde implements IConfigurable {
				public $init = false;
				public $setup = 0;

				protected function handleInit(): void {
					$this->init = true;
				}

				protected function handleSetup(): void {
					$this->setup++;
				}
			};
			$sample->setup();
			$sample->setup();
			self::assertTrue($sample->init);
			self::assertEquals(1, $sample->setup);
		}
	}
