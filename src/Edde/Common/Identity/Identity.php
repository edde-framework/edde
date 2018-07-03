<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Acl\IAcl;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Identity\IdentityException;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Deffered\AbstractDeffered;

	class Identity extends AbstractDeffered implements IIdentity {
		/**
		 * @var ICrate
		 */
		protected $identity;
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var bool
		 */
		protected $authenticated;
		/**
		 * @var array
		 */
		protected $metaList = [];
		/**
		 * @var IAcl
		 */
		protected $acl;

		public function __construct() {
			$this->name = 'unknown';
			$this->authenticated = false;
		}

		public function getMeta(string $name, $default = null) {
			return $this->metaList[$name] ?? ($default && is_callable($default) ? call_user_func($default) : $default);
		}

		public function getMetaList(): array {
			return $this->metaList;
		}

		public function setMetaList(array $metaList): IIdentity {
			$this->metaList = $metaList;
			return $this;
		}

		public function hasIdentity(): bool {
			return $this->identity !== null;
		}

		public function getIdentity(): ICrate {
			if ($this->identity === null) {
				throw new IdentityException(sprintf('Identity [%s] has no additional data.', $this->name));
			}
			return $this->identity;
		}

		public function setIdentity(ICrate $identity = null): IIdentity {
			$this->identity = $identity;
			return $this;
		}

		public function getName(): string {
			return $this->name;
		}

		public function setName(string $name): IIdentity {
			$this->name = $name;
			return $this;
		}

		public function isAuthenticated(): bool {
			return $this->authenticated;
		}

		public function setAuthenticated(bool $authenticated): IIdentity {
			$this->authenticated = $authenticated;
			return $this;
		}

		public function setAcl(IAcl $acl): IIdentity {
			$this->acl = $acl;
			return $this;
		}

		public function getAcl(): IAcl {
			if ($this->acl === null) {
				throw new IdentityException(sprintf('Identity [%s] has no acl set.', $this->getName()));
			}
			return $this->acl;
		}

		public function can(string $resource, \DateTime $dateTime = null): bool {
			return $this->acl ? $this->acl->can($resource, $dateTime) : $this->isAuthenticated();
		}
	}
