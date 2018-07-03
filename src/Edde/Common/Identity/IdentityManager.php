<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Identity\IIdentity;
	use Edde\Api\Identity\IIdentityManager;
	use Edde\Api\Storage\LazyStorageTrait;
	use Edde\Common\Deffered\DefferedTrait;
	use Edde\Common\Session\SessionTrait;
	use Edde\Common\Storage\AbstractRepository;

	class IdentityManager extends AbstractRepository implements ILazyInject, IIdentityManager {
		use LazyStorageTrait;
		use SessionTrait;
		use DefferedTrait;

		const SESSION_IDENTITY = 'identity';

		/**
		 * @var IIdentity
		 */
		protected $identity;

		public function update(): IIdentityManager {
			$this->use();
			$this->session->set(self::SESSION_IDENTITY, $this->identity());
			return $this;
		}

		public function identity(): IIdentity {
			$this->use();
			if ($this->identity === null) {
				$this->identity = $this->session->get(self::SESSION_IDENTITY, new Identity());
			}
			return $this->identity;
		}

		public function reset(bool $hard = true): IIdentityManager {
			$this->use();
			$this->session->set(self::SESSION_IDENTITY, null);
			$this->identity();
			if ($hard) {
				$this->identity->setMetaList([]);
				$this->identity->setName('');
			}
			return $this;
		}

		protected function prepare() {
			$this->session();
		}
	}
