<?php
	declare(strict_types=1);

	namespace Edde\Common\Store;

	use Edde\Api\Store\IStore;
	use Edde\Api\Store\IStoreManager;
	use Edde\Common\Config\ConfigurableTrait;

	abstract class AbstractStoreManager extends AbstractStore implements IStoreManager {
		use ConfigurableTrait;
		/**
		 * @var IStore[]
		 */
		protected $storeList = [];
		/**
		 * @var IStore
		 */
		protected $current;
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var \SplStack
		 */
		protected $stack;

		/**
		 * @inheritdoc
		 */
		public function set(string $name, $value): IStore {
			$this->current->set($name, $value);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function has(string $name): bool {
			return $this->current->has($name);
		}

		/**
		 * @inheritdoc
		 */
		public function get(string $name, $default = null) {
			return $this->current->get($name, $default);
		}

		/**
		 * @inheritdoc
		 */
		public function iterate() {
			return $this->current->iterate();
		}

		/**
		 * @inheritdoc
		 */
		public function remove(string $name): IStore {
			$this->current->remove($name);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function drop(): IStore {
			$this->current->drop();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerStore(IStore $store, string $name = null): IStoreManager {
			$this->storeList[$name ?: get_class($store)] = $store;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function select(string $name): IStoreManager {
			if (isset($this->storeList[$name]) === false) {
				throw new UnknownStoreException(sprintf('Requested store [%s] which is not known in current store manager.' . ($this->isSetup() ? ' Manager has been set up.' : 'Manager has not been set up, try to call ::setup() method.'), $name));
			}
			$this->stack->push([
				$this->current,
				$this->name,
			]);
			$this->current = $this->storeList[$name];
			$this->current->setup();
			$this->name = $name;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function restore(): IStoreManager {
			list($this->current, $this->name) = $this->stack->pop();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getCurrentStore(): IStore {
			return $this->current;
		}

		/**
		 * @inheritdoc
		 */
		public function getCurrentName(): string {
			return $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function save(string $name, $value, string $store): IStoreManager {
			$this->select($store);
			try {
				$this->current->set($name, $value);
				return $this;
			} finally {
				$this->restore();
			}
		}

		/**
		 * @inheritdoc
		 */
		public function load(string $name, string $store, $default = null) {
			$this->select($store);
			try {
				return $this->current->get($name, $default);
			} finally {
				$this->restore();
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function handleInit() {
			parent::handleInit();
			$this->stack = new \SplStack();
		}

		/**
		 * @inheritdoc
		 */
		public function handleSetup() {
			parent::handleSetup();
			$this->current->setup();
		}
	}
