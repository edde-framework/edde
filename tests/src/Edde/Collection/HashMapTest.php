<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\TestCase;

	class HashMapTest extends TestCase {
		public function testList() {
			$hashMap = new HashMap();
			self::assertTrue($hashMap->isEmpty());
			$hashMap->put($object = (object)['a' => 1, 'b' => 2]);
			self::assertFalse($hashMap->isEmpty());
			self::assertEquals($object, $hashMap->toObject());
			self::assertTrue($hashMap->has('a'));
			$hashMap->remove('a');
			self::assertFalse($hashMap->has('a'));
			$hashMap->clear();
			self::assertTrue($hashMap->isEmpty());
			$hashMap->put((object)['a' => 1]);
			$hashMap->merge((object)['b' => 2]);
			$hashMap->set('boo', $inner = new HashMap());
			$inner->set('foo', 'bar');
			$inner->set('bar', 'foo');
			$object->boo = (object)[
				'foo' => 'bar',
				'bar' => 'foo',
			];
			self::assertEquals($object, $hashMap->toObject());
		}
	}
