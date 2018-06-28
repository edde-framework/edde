<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\TestCase;

	class UuidFilterTest extends TestCase {
		public function testInput() {
			$this->container->inject($filter = new UuidFilter());
			self::assertSame('prd', $filter->input('prd'));
			self::assertSame('62653036-3337-4564-b635-613138613763', $filter->input(null, ['seed' => 'prd']));
		}

		public function testOutout() {
			$this->container->inject($filter = new UuidFilter());
			self::assertSame('prd', $filter->output('prd'));
		}
	}
