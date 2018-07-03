<?php
	declare(strict_types=1);

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Config\IConfigurator;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	interface ISomething {
		public function registerSomeething(string $something);
	}

	class FirstSomethingSetup extends AbstractConfigurator implements IConfigurator {
		/**
		 * @param ISomething $instance
		 */
		public function configure($instance) {
			$instance->registerSomeething('foo');
		}

		public function getBoo() {
			return 'boo';
		}
	}

	class AnotherSomethingSetup extends AbstractConfigurator implements IConfigurator {
		use LazyContainerTrait;

		/**
		 * @param ISomething $instance
		 */
		public function configure($instance) {
			$instance->registerSomeething('bar');
			$instance->registerSomeething($this->container->create(FirstSomethingSetup::class)
				->getBoo());
		}
	}

	class Something extends Object implements ISomething, IConfigurable {
		use ConfigurableTrait;
		public $someParameter;
		public $anotherSomething;
		public $injectedSomething;
		public $lazySomething;
		public $anotherAnotherSomething;
		public $somethingList = [];

		public function __construct($someParameter, AnotherSomething $anotherSomething) {
			$this->someParameter = $someParameter;
			$this->anotherSomething = $anotherSomething;
		}

		public function injectSomething(InjectedSomething $injectedSomething) {
		}

		public function lazySomething(LazySomething $lazySomething, AnotherAnotherSomething $anotherAnotherSomething) {
		}

		public function registerSomeething(string $something) {
			$this->somethingList[] = $something;
			return $this;
		}
	}

	class AnotherSomething extends Object implements IConfigurable {
		use ConfigurableTrait;
		public $init = 0;
		public $warmup = 0;
		public $config = 0;
		public $setup = 0;
		public $ok = false;

		protected function handleInit() {
			$this->init++;
		}

		protected function handleWarmup() {
			$this->warmup++;
		}

		protected function handleConfig() {
			$this->config++;
		}

		protected function handleSetup() {
			$this->setup++;
		}
	}

	class AnotherSomethingConfigurator extends AbstractConfigurator {
		/**
		 * @param AnotherSomething $instance
		 */
		public function configure($instance) {
			$instance->ok = true;
		}
	}

	class InjectedSomething extends Object {
	}

	class LazySomething extends Object {
	}

	class AnotherAnotherSomething extends Object {
	}

	class ThisIsCleverManager extends Object {
		/**
		 * @var AnotherSomething
		 */
		protected $anotherSomething;
		/**
		 * @var InjectedSomething
		 */
		protected $injectedSomething;

		/**
		 * @param AnotherSomething  $anotherSomething
		 * @param InjectedSomething $injectedSomething
		 */
		public function __construct(AnotherSomething $anotherSomething, InjectedSomething $injectedSomething) {
			$this->anotherSomething = $anotherSomething;
			$this->injectedSomething = $injectedSomething;
		}

		public function createCleverProduct(): ThisIsProductOfCleverManager {
			return new ThisIsProductOfCleverManager();
		}
	}

	class ThisIsProductOfCleverManager extends Object {
	}
