<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity\Authenticator;

	use Edde\Api\Identity\IAuthenticatorManager;
	use Edde\Api\Identity\IAuthorizator;
	use Edde\Api\Identity\IIdentity;
	use Edde\Api\Identity\IIdentityManager;
	use Edde\Api\Session\ISessionManager;
	use Edde\Common\Identity\AuthenticatorManager;
	use Edde\Common\Identity\IdentityManager;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class AuthenticatorManagerTest extends TestCase {
		/**
		 * @var IAuthenticatorManager
		 */
		protected $authenticatorManager;
		/**
		 * @var IIdentity
		 */
		protected $identity;
		/**
		 * @var ISessionManager
		 */
		protected $sessionManager;

		public function testFlow() {
			self::assertEquals('unknown', $this->identity->getName());
			self::assertFalse($this->identity->isAuthenticated());
			self::assertFalse($this->identity->can('foobar'));
			$this->authenticatorManager->select('flow');
			$this->authenticatorManager->flow(\InitialAuthenticator::class, 'foo', 'bar');
			self::assertEquals('whepee', $this->identity->getName());
			self::assertFalse($this->identity->isAuthenticated());
			self::assertEquals(\SecondaryAuthenticator::class, $this->authenticatorManager->getCurrentFlow());
			$this->authenticatorManager->flow(\SecondaryAuthenticator::class, 'boo', 'poo');
			self::assertEquals('whepee', $this->identity->getName());
			self::assertTrue($this->identity->isAuthenticated());
			self::assertTrue($this->identity->can('foobar'));
			self::assertNull($this->authenticatorManager->getCurrentFlow());
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				IAuthenticatorManager::class => AuthenticatorManager::class,
				IIdentityManager::class => IdentityManager::class,
				IIdentity::class => function (IIdentityManager $identityManager) {
					return $identityManager->identity();
				},
				ISessionManager::class => \DummySession::class,
				IAuthorizator::class => \TrustedAuth::class,
			]);
			$this->authenticatorManager = $container->create(IAuthenticatorManager::class);
			$this->authenticatorManager->registerAuthenticator(new \TrustedAuthenticator());
			$this->authenticatorManager->registerAuthenticator(new \InitialAuthenticator());
			$this->authenticatorManager->registerAuthenticator(new \SecondaryAuthenticator());
			$this->authenticatorManager->registerFlow('flow', \InitialAuthenticator::class, \SecondaryAuthenticator::class);
			$this->sessionManager = $container->create(ISessionManager::class);
			$this->identity = $container->create(IIdentityManager::class)
				->identity();
		}

		protected function tearDown() {
			$this->sessionManager->close();
		}
	}
