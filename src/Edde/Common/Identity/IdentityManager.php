<?php
	declare(strict_types=1);

	namespace Edde\Common\Identity;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Identity\IIdentity;
	use Edde\Api\Identity\IIdentityManager;
	use Edde\Api\Identity\ProxyIdentityTrait;
	use Edde\Api\Storage\LazyStorageTrait;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Session\SessionTrait;
	use Edde\Common\Storage\AbstractRepository;

	class IdentityManager extends AbstractRepository implements IIdentityManager {
		use LazyContainerTrait;
		use LazyStorageTrait;
		use SessionTrait;
		use ConfigurableTrait;
		use ProxyIdentityTrait;

		/**
		 * @inheritdoc
		 */
		public function createIdentity(): IIdentity {
			return $this->identity ?: $this->identity = $this->session->get(IIdentity::class, $this->container->create(Identity::class)->setup());
		}

		/**
		 * @inheritdoc
		 */
		public function update(IIdentity $identity = null): IIdentityManager {
			$identity = $identity === $this ? null : $identity;
			$this->session->set(IIdentity::class, $this->identity = $identity ?: $this->createIdentity());
			return $this;
		}

		protected function handleSetup() {
			parent::handleSetup();
			$this->session();
			$this->createIdentity();
		}
	}
