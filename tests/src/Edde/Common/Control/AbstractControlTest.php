<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control;

	use Edde\Api\Control\ControlException;
	use Edde\Common\Control\Event\DoneEvent;
	use Edde\Common\Control\Event\HandleEvent;
	use Edde\Common\Html\Tag\DivControl;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class AbstractControlTest extends TestCase {
		public function testCommon() {
			$control = new \TestControl();
			$controlList = [$control];
			$control->addControl($controlList[] = $another = new \TestControl());
			$control->addControlList([$controlList[] = new \TestControl()]);
			self::assertFalse($control->isLeaf());
			self::assertTrue($another->isLeaf());
			self::assertEmpty($control->getParent());
			self::assertSame($control, $control->getRoot());
			self::assertSame($control, $another->getRoot());
			self::assertSame($control, $another->getParent());
			self::assertSame($controlList, iterator_to_array($control));

			$eventList = [];
			$control->listen(function (HandleEvent $handleEvent) use (&$eventList) {
				$eventList[] = get_class($handleEvent);
			});
			$control->listen(function (DoneEvent $doneEvent) use (&$eventList) {
				$eventList[] = get_class($doneEvent);
			});
			self::assertEquals('poofoo', $control->handle('someMethod', [
				'foo' => 'poo',
				'boo' => 'foo',
			]));
			self::assertEquals([
				HandleEvent::class,
				DoneEvent::class,
			], $eventList);
		}

		public function testHandleException() {
			$this->expectException(ControlException::class);
			$this->expectExceptionMessage('Missing action parameter [TestControl::someMethod(, ...$boo, ...)].');
			$control = new \TestControl();
			self::assertEquals('pooboo', $control->handle('someMethod', ['foo' => 'poo',]));
		}

		public function testDummyHandle() {
			$control = new \TestControl();
			self::assertEquals('dumyyyy', $control->handle('dummy', []));
		}

		public function testFill() {
			$control = new \TestControl();
			$control->fill([
				'thisIsArray' => $expectedArray = [
					'foo' => 'bar',
					'another-foo' => 'anothezr-bar',
				],
				'singleValue' => 'boo',
			]);
			self::assertEquals($expectedArray, $control->thisIsArray);
		}

		public function testToggleClass() {
			$control = new DivControl();
			$control->toggleClass('foo');
			self::assertEquals('<div class="foo"></div>
', $control->render());
			$control->toggleClass('foo');
			self::assertEquals('<div></div>
', $control->render());
			$control->toggleClass('foo');
			self::assertEquals('<div class="foo"></div>
', $control->render());
			$control->toggleClass('foo', true);
			self::assertEquals('<div class="foo"></div>
', $control->render());
			$control->toggleClass('foo', false);
			self::assertEquals('<div></div>
', $control->render());
		}

		public function testToggleClass2() {
			$control = new DivControl();
			$control->toggleClass('foo');
			$control->toggleClass('bar');
			self::assertEquals('<div class="foo bar"></div>
', $control->render());
			$control->toggleClass('foo');
			self::assertEquals('<div class="bar"></div>
', $control->render());
			$control->toggleClass('foo');
			self::assertEquals('<div class="bar foo"></div>
', $control->render());
			$control->toggleClass('foo', true);
			self::assertEquals('<div class="bar foo"></div>
', $control->render());
			$control->toggleClass('foo', false);
			self::assertEquals('<div class="bar"></div>
', $control->render());
		}
	}
