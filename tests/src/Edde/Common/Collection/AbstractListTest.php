<?php
	declare(strict_types = 1);

	namespace Edde\Common\Collection;

	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class AbstractListTest extends TestCase {
		public function testCommon() {
			$list = new \SimpleList();
			self::assertTrue($list->isEmpty());
			self::assertFalse($list->has('foo'));
			$list->set('foo', 'poo');
			$list->set('boo', 'goo');
			self::assertTrue($list->has('foo'));
			self::assertEquals('poo', $list->get('foo'));
			self::assertFalse($list->isEmpty());
			self::assertEquals($expect = [
				'foo' => 'poo',
				'boo' => 'goo',
			], $list->array());
			self::assertEquals($expect, iterator_to_array($list));
			$list->put([]);
			self::assertTrue($list->isEmpty());
			$list->put($expect);
			self::assertEquals($expect = [
				'foo' => 'poo',
				'boo' => 'goo',
			], $list->array());
			self::assertEquals($expect, iterator_to_array($list));
		}
	}
