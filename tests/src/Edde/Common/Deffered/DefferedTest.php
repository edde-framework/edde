<?php
	declare(strict_types = 1);

	namespace Edde\Common\Deffered;

	use phpunit\framework\TestCase;

	require_once __DIR__ . '/assets.php';

	/**
	 * Deffered class related tests.
	 */
	class DefferedTest extends TestCase {
		public function testUsableObject() {
			$object = new UsableObject();
			$onDefferedFlag = false;
			self::assertFalse($object->prepared);
			self::assertFalse($object->isUsed());
			$object->onDeffered(function () use (&$onDefferedFlag) {
				$onDefferedFlag = true;
			});
			$object->takeAction();
			self::assertTrue($onDefferedFlag);
			self::assertTrue($object->prepared);
			self::assertTrue($object->isUsed());
		}

		public function testUseHook() {
			$object = new UsableObject();
			$onSetupdFlag = false;
			$object->onSetup(function () use (&$onSetupdFlag) {
				$onSetupdFlag = true;
			});
			self::assertFalse($onSetupdFlag);
			$object->takeAction();
			self::assertTrue($onSetupdFlag);
		}

		public function testSetupHook() {
			$object = new UsableObject();
			$onDefferedFlag = false;
			$object->onDeffered(function () use (&$onDefferedFlag) {
				$onDefferedFlag = true;
			});
			self::assertFalse($onDefferedFlag);
			$object->takeAction();
			self::assertTrue($onDefferedFlag);
		}

		public function testSetupOrder() {
			$object = new UsableObject();
			$order = [];
			$object->onDeffered(function () use (&$order) {
				$order[] = 'setup';
			});
			$object->onSetup(function () use (&$order) {
				$order[] = 'use';
			});
			$object->takeAction();
			self::assertEquals([
				'setup',
				'use',
			], $order);
		}

		public function testAfterDeffered() {
			$this->expectException(DefferedException::class);
			$this->expectExceptionMessage('Cannot add Edde\Common\Deffered\UsableObject::onDeffered() callback to already used class [Edde\Common\Deffered\UsableObject].');
			$object = new UsableObject();
			$object->takeAction();
			$object->onDeffered(function () {
			});
		}

		public function testAfterSetup() {
			$this->expectException(DefferedException::class);
			$this->expectExceptionMessage('Cannot add Edde\Common\Deffered\UsableObject::onSetup() callback to already used class [Edde\Common\Deffered\UsableObject].');
			$object = new UsableObject();
			$object->takeAction();
			$object->onSetup(function () {
			});
		}

		public function testOnLoadedHook() {
			$object = new UsableObject();
			$onLoadedFlag = false;
			$object->onLoaded(function () use (&$onLoadedFlag) {
				$onLoadedFlag = true;
			});
			self::assertFalse($onLoadedFlag);
			$object->takeAction();
			self::assertTrue($onLoadedFlag);
		}

		public function testOnLoadedHookImmediate() {
			$object = new UsableObject();
			$onLoadedFlag = false;
			$object->takeAction();
			$object->onLoaded(function () use (&$onLoadedFlag) {
				$onLoadedFlag = true;
			});
			self::assertTrue($onLoadedFlag);
		}

		public function testUsableTraitedObject() {
			$object = new UsableTraitedObject();
			self::assertFalse($object->prepared);
			$object->takeAction();
			self::assertTrue($object->prepared);
		}
	}
