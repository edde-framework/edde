<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\TestCase;

	class ListTest extends TestCase {
		public function testList() {
			$list = new TestList();
			self::assertTrue($list->isEmpty());
			$list->put($array = ['a' => 1, 'b' => 2]);
			self::assertFalse($list->isEmpty());
			self::assertEquals($array, $list->array());
			self::assertTrue($list->has('a'));
			$list->remove('a');
			self::assertFalse($list->has('a'));
			$list->clear();
			self::assertTrue($list->isEmpty());
			$list->put(['a' => 1]);
			$list->merge(['b' => 2]);
			self::assertEquals($array, $list->array());
		}
	}
