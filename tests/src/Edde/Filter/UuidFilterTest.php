<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\TestCase;

	class UuidFilterTest extends TestCase {
		public function testInput() {
			$this->container->inject($filter = new UuidFilter());
			self::assertSame('prd', $filter->input('prd'));
		}

		public function testOutput() {
			$this->container->inject($filter = new UuidFilter());
			self::assertSame('prd', $filter->output('prd'));
			self::assertSame('62653036-3337-4564-b635-613138613763', $filter->output(null, ['seed' => 'prd']));
		}
	}
