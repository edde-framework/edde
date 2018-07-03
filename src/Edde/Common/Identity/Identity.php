<?php
	declare(strict_types=1);

	namespace Edde\Common\Identity;

	use Edde\Api\Acl\IAcl;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Identity\IdentityException;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	class Identity extends Object implements IIdentity {
		use ConfigurableTrait;
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

		/**
		 * @inheritdoc
		 */
		public function getMetaList(): array {
			return $this->metaList;
		}

		/**
		 * @inheritdoc
		 */
		public function setMetaList(array $metaList): IIdentity {
			$this->metaList = $metaList;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setMeta(string $name, $value): IIdentity {
			$this->metaList[$name] = $value;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getMeta(string $name, $default = null) {
			return $this->metaList[$name] ?? ($default && is_callable($default) ? call_user_func($default) : $default);
		}

		/**
		 * @inheritdoc
		 */
		public function hasIdentity(): bool {
			return $this->identity !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function getIdentity(): ICrate {
			if ($this->identity === null) {
				throw new IdentityException(sprintf('Identity [%s] has no additional data.', $this->name));
			}
			return $this->identity;
		}

		/**
		 * @inheritdoc
		 */
		public function setIdentity(ICrate $identity = null): IIdentity {
			$this->identity = $identity;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function setName(string $name): IIdentity {
			$this->name = $name;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isAuthenticated(): bool {
			return $this->authenticated;
		}

		/**
		 * @inheritdoc
		 */
		public function setAuthenticated(bool $authenticated): IIdentity {
			$this->authenticated = $authenticated;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setAcl(IAcl $acl): IIdentity {
			$this->acl = $acl;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getAcl(): IAcl {
			if ($this->acl === null) {
				throw new IdentityException(sprintf('Identity [%s] has no acl set.', $this->getName()));
			}
			return $this->acl;
		}

		/**
		 * @inheritdoc
		 */
		public function can(string $resource, \DateTime $dateTime = null): bool {
			return $this->acl ? $this->acl->can($resource, $dateTime) : $this->isAuthenticated();
		}

		/**
		 * @inheritdoc
		 */
		public function reset(): IIdentity {
			$this->identity = null;
			$this->name = 'unknown';
			$this->metaList = [];
			$this->authenticated = false;
			return $this;
		}
	}
