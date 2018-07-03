<?php
	declare(strict_types=1);

	namespace Edde\Api\Identity;

	use Edde\Api\Acl\IAcl;
	use Edde\Api\Crate\ICrate;

	trait ProxyIdentityTrait {
		/**
		 * @var IIdentity
		 */
		protected $identity;

		public function setIdentity(ICrate $crate = null): IIdentity {
			$this->identity->setIdentity($crate);
			return $this;
		}

		public function hasIdentity(): bool {
			return $this->identity->hasIdentity();
		}

		public function getIdentity(): ICrate {
			return $this->identity->getIdentity();
		}

		public function setMetaList(array $metaList): IIdentity {
			$this->identity->setMetaList($metaList);
			return $this;
		}

		public function setMeta(string $name, $value): IIdentity {
			$this->identity->setMeta($name, $value);
			return $this;
		}

		public function getMeta(string $name, $default = null) {
			return $this->identity->getMeta($name, $default);
		}

		public function getMetaList(): array {
			return $this->identity->getMetaList();
		}

		public function setName(string $name): IIdentity {
			$this->identity->setName($name);
			return $this;
		}

		public function getName(): string {
			return $this->identity->getName();
		}

		public function setAuthenticated(bool $authenticated): IIdentity {
			$this->identity->setAuthenticated($authenticated);
			return $this;
		}

		public function isAuthenticated(): bool {
			return $this->identity->isAuthenticated();
		}

		public function setAcl(IAcl $acl): IIdentity {
			$this->identity->setAcl($acl);
			return $this;
		}

		public function getAcl(): IAcl {
			return $this->identity->getAcl();
		}

		public function can(string $resource, \DateTime $dateTime = null): bool {
			return $this->identity->can($resource, $dateTime);
		}

		public function reset(): IIdentity {
			$this->identity->reset();
			return $this;
		}
	}
