<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event\Handler;

	use Edde\Api\Event\EventException;
	use Edde\Api\Event\IEvent;
	use Edde\Common\Deffered\DefferedTrait;
	use Edde\Common\Event\AbstractHandler;

	/**
	 * This should take instance on input and return all methods accepting exactly one IEvent parameter.
	 */
	class ReflectionHandler extends AbstractHandler {
		use DefferedTrait;
		protected $handler;
		/**
		 * @var array[]
		 */
		protected $methodList = [];

		/**
		 * Pessimist: "Things just can't get any worse!"
		 *
		 * Optimist: "Nah, of course they can!"
		 *
		 * @param string $handler
		 * @param string|null $scope
		 */
		public function __construct($handler, string $scope = null) {
			parent::__construct($scope);
			$this->handler = $handler;
		}

		/**
		 * @inheritdoc
		 */
		public function getIterator() {
			$this->use();
			foreach ($this->methodList as $event => $closureList) {
				foreach ($closureList as $closure) {
					yield $event => $closure;
				}
			}
		}

		/**
		 * @inheritdoc
		 * @throws EventException
		 */
		protected function prepare() {
			/**
			 * @var $cache \ReflectionMethod[][][]
			 */
			static $cache = [];
			if (isset($cache[$className = get_class($this->handler)]) === false) {
				$cache[$className] = [];
				$reflectionClass = new \ReflectionClass($this->handler);
				foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
					if ($reflectionMethod->getNumberOfParameters() !== 1) {
						continue;
					}
					/** @var $reflectionParameter \ReflectionParameter */
					$reflectionParameter = $reflectionMethod->getParameters()[0];
					if (($class = $reflectionParameter->getClass()) === null) {
						continue;
					}
					if (in_array(IEvent::class, $class->getInterfaceNames(), true) === false) {
						continue;
					}
					$cache[$className][$class = $class->getName()][] = $reflectionMethod;
					$this->methodList[$class][] = $reflectionMethod->getClosure($this->handler);
				}
			} else {
				foreach ($cache[$className] as $class => $methodList) {
					foreach ($methodList as $method) {
						$this->methodList[$class][] = $method->getClosure($this->handler);
					}
				}
			}
		}
	}
