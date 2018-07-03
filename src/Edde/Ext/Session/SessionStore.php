<?php
	declare(strict_types=1);

	namespace Edde\Ext\Session;

	use Edde\Api\Session\ISession;
	use Edde\Api\Session\LazySessionManagerTrait;
	use Edde\Api\Store\IStore;
	use Edde\Common\Store\AbstractStore;

	class SessionStore extends AbstractStore {
		use LazySessionManagerTrait;
		/**
		 * @var string
		 */
		protected $namespace;
		/**
		 * @var ISession
		 */
		protected $session;

		public function __construct(string $namespace = null) {
			$this->namespace = $namespace ?: static::class;
		}

		/**
		 * @inheritdoc
		 */
		public function set(string $name, $value): IStore {
			$this->session->set($name, [
				$name,
				$value,
				[],
			]);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function iterate() {
			foreach ($this->session as list($name, $value)) {
				yield $name => $value;
			}
		}

		/**
		 * @inheritdoc
		 */
		public function has(string $name): bool {
			return $this->session->has($name);
		}

		/**
		 * @inheritdoc
		 */
		public function get(string $name, $default = null) {
			if (($value = $this->session->get($name)) === null) {
				return $default;
			}
			list(, $value) = $value;
			return $value;
		}

		/**
		 * @inheritdoc
		 */
		public function remove(string $name): IStore {
			$this->session->remove($name);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function drop(): IStore {
			$this->session->clear();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		protected function handleSetup() {
			parent::handleSetup();
			$this->sessionManager->setup();
			$this->session = $this->sessionManager->getSession($this->namespace);
		}
	}
