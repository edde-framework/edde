<?php
	declare(strict_types = 1);

	namespace Edde\Common\Acl;

	use Edde\Api\Acl\AclException;
	use Edde\Api\Acl\IAclManager;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	class AclTest extends TestCase {
		/**
		 * @var IAclManager
		 */
		protected $aclManager;

		public function testException() {
			$this->expectException(AclException::class);
			$this->expectExceptionMessage('Unknown group [some, unknown, group]. Did you register access for this group(s)?');
			$this->aclManager->acl([
				'some',
				'unknown',
				'group',
			]);
		}

		public function testRootAcl() {
			$acl = $this->aclManager->acl(['root']);
			self::assertTrue($acl->can('something'), 'Root has missing right!');
			self::assertFalse($acl->can('be-stupid'), 'Root can be stupid, oops!');
		}

		public function testGuestAcl() {
			$acl = $this->aclManager->acl(['guest']);
			self::assertFalse($acl->can('something'), 'Guest can do something!');
			self::assertTrue($acl->can('file.read'));
			self::assertFalse($acl->can('file.delete'));
		}

		public function testGuestTimeAcl() {
			$acl = $this->aclManager->acl(['guest']);
			self::assertFalse($acl->can('time', new \DateTime('31.12.1989')), 'Guest can do something in wrong time!');
			self::assertTrue($acl->can('time', new \DateTime('1.1.1990')), 'Guest can NOT do something in good time!');
			self::assertTrue($acl->can('time', new \DateTime('2.1.1990')), 'Guest can NOT do something in good time!');
			self::assertTrue($acl->can('time', new \DateTime('5.1.1990')), 'Guest can NOT do something in good time!');
			self::assertFalse($acl->can('time', new \DateTime('6.1.1990')), 'Guest can do something in wrong time!');

			self::assertFalse($acl->can('from', new \DateTime('31.12.1989')), 'Guest can do something in wrong time!');
			self::assertTrue($acl->can('from', new \DateTime('1.1.1990')), 'Guest can NOT do something in good time!');
			self::assertTrue($acl->can('from', new \DateTime('2.1.1990')), 'Guest can NOT do something in good time!');

			self::assertTrue($acl->can('until', new \DateTime('1.1.1989')), 'Guest can NOT do something in good time!');
			self::assertTrue($acl->can('until', new \DateTime('1.1.1990')), 'Guest can NOT do something in good time!');
			self::assertFalse($acl->can('until', new \DateTime('2.1.1990')), 'Guest can do something in wrong time!');
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				IAclManager::class => AclManager::class,
			]);
			$this->aclManager = $container->create(IAclManager::class);
			$this->aclManager->grant('root');
			$this->aclManager->deny('root', 'be-stupid');
			$this->aclManager->deny('guest');
			$this->aclManager->grant('guest', 'file.read');
			$this->aclManager->grant('guest', 'file.delete');
			$this->aclManager->deny('guest', 'file.delete');
			$this->aclManager->grant('guest', 'time', new \DateTime('1.1.1990'), new \DateTime('5.1.1990'));
			$this->aclManager->grant('guest', 'from', new \DateTime('1.1.1990'));
			$this->aclManager->grant('guest', 'until', null, new \DateTime('1.1.1990'));
		}
	}
