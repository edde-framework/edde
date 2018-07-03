<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control;

	use Edde\Api\Callback\ICallback;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Control\ControlException;
	use Edde\Api\Control\IControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\Callback\Callback;
	use Edde\Common\Control\Event\CancelEvent;
	use Edde\Common\Control\Event\DoneEvent;
	use Edde\Common\Control\Event\HandleEvent;
	use Edde\Common\Control\Event\UpdateEvent;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\Event\EventTrait;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Root implementation of all controls.
	 */
	abstract class AbstractControl extends AbstractDeffered implements IControl {
		use LazyContainerTrait;
		use EventTrait;
		/**
		 * @var INode
		 */
		protected $node;

		/**
		 * @inheritdoc
		 */
		public function getNode(): INode {
			$this->use();
			return $this->node;
		}

		/**
		 * @inheritdoc
		 */
		public function getRoot(): IControl {
			$this->use();
			if ($this->node->isRoot()) {
				return $this;
			}
			/** @var $rootNode INode */
			$rootNode = $this->node->getRoot();
			return $rootNode->getMeta('control');
		}

		/**
		 * @inheritdoc
		 */
		public function getParent() {
			$this->use();
			$parent = $this->node->getParent();
			return $parent ? $parent->getMeta('control') : null;
		}

		/**
		 * @inheritdoc
		 */
		public function isLeaf(): bool {
			$this->use();
			return $this->node->isLeaf();
		}

		/**
		 * @inheritdoc
		 */
		public function disconnect(): IControl {
			$this->use();
			if ($this->node->isRoot() === false) {
				$this->node->getParent()
					->removeNode($this->node);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function addControlList(array $controlList): IControl {
			foreach ($controlList as $control) {
				$this->addControl($control);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function addControl(IControl $control): IControl {
			$this->use();
			$this->node->addNode($control->getNode(), true);
			$control->attached($this);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function attached(IControl $control): IControl {
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isDirty(): bool {
			$this->use();
			return $this->node->getMeta('dirty', false);
		}

		/**
		 * @inheritdoc
		 */
		public function dirty(bool $dirty = true): IControl {
			$this->use();
			$this->node->setMeta('dirty', $dirty);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getControlList(): array {
			$controlList = [];
			foreach ($this->node->getNodeList() as $node) {
				$controlList[] = $node->getMeta('control');
			}
			return $controlList;
		}

		/**
		 * @inheritdoc
		 */
		public function invalidate(): array {
			$this->use();
			$invalidList = [];
			foreach ($this as $control) {
				if ($control->isDirty()) {
					$invalidList[] = $control;
				}
			}
			return $invalidList;
		}

		/**
		 * @inheritdoc
		 */
		public function update(): IControl {
			$this->use();
			$this->event(new UpdateEvent($this));
			foreach ($this->getControlList() as $control) {
				$control->update();
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws ControlException
		 */
		public function fill($fill): IControl {
			$this->use();
			$reflectionClass = new \ReflectionClass($this);
			/** @noinspection ForeachSourceInspection */
			foreach ($fill as $k => $v) {
				if ($reflectionClass->hasProperty($k) === false) {
					throw new ControlException(sprintf('Unknown property [%s::$%s] to fill.', static::class, $k));
				}
				$reflectionProperty = $reflectionClass->getProperty($k);
				$reflectionProperty->setAccessible(true);
				$reflectionProperty->setValue($this, $v);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws ControlException
		 */
		public function handle(string $method, array $parameterList) {
			$this->use();
			$this->event($handleEvent = new HandleEvent($this, $method, $parameterList));
			if ($handleEvent->isCanceled()) {
				$this->event(new CancelEvent($this));
				return null;
			}
			$result = $this->execute($method, $parameterList);
			$this->update();
			$this->event(new DoneEvent($this, $result));
			return $result;
		}

		/**
		 * @param string $method
		 * @param array $parameterList
		 *
		 * @return mixed
		 * @throws ControlException
		 */
		protected function execute(string $method, array $parameterList) {
			$argumentList = array_filter($parameterList, function ($key) {
				return is_int($key);
			}, ARRAY_FILTER_USE_KEY);
			if (isset($parameterList[null])) {
				$this->fill($parameterList[null]);
			}
			if (method_exists($this, $method)) {
				/** @var $callback ICallback */
				$callback = new Callback([
					$this,
					$method,
				]);
				$argumentCount = count($argumentList);
				foreach ($callback->getParameterList() as $key => $parameter) {
					if (--$argumentCount >= 0) {
						continue;
					}
					if (isset($parameterList[$parameterName = StringUtils::recamel($parameter->getName())]) === false) {
						if ($parameter->isOptional()) {
							continue;
						}
						throw new ControlException(sprintf('Missing action parameter [%s::%s(, ...$%s, ...)].', static::class, $method, $parameter->getName()));
					}
					$argumentList[] = $parameterList[$parameterName];
				}
				return $callback->invoke(...$argumentList);
			}
			return $this->action(StringUtils::recamel($method), $argumentList);
		}

		/**
		 * when handle method does not exists, this generic method will be executed
		 *
		 * @param string $action
		 * @param array $parameterList
		 *
		 * @throws ControlException
		 */
		protected function action(string $action, array $parameterList) {
			throw new ControlException(sprintf('Unknown handle method [%s]; to disable this exception, override [%s::%s()] method or implement [%s::%s()].', $action, static::class, __FUNCTION__, static::class, StringUtils::toCamelHump($action)));
		}

		public function createControl(string $control, ...$parameterList): IControl {
			return $this->container->create($control, ...$parameterList);
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function getIterator() {
			$this->use();
			foreach (NodeIterator::recursive($this->node, true) as $node) {
				yield $node->getMeta('control');
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			$this->listen($this);
			$this->node = new Node();
			$this->node->setMeta('control', $this);
			return $this;
		}
	}
