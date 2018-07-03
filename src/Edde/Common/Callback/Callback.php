<?php
	declare(strict_types=1);

	namespace Edde\Common\Callback;

	use Edde\Api\Callback\ICallback;
	use Edde\Common\Object;
	use Edde\Common\Reflection\ReflectionUtils;

	class Callback extends Object implements ICallback {
		/**
		 * @var callable
		 */
		protected $callback;
		protected $parameterList;

		/**
		 * @param callable $callback
		 */
		public function __construct(callable $callback) {
			$this->callback = $callback;
		}

		/**
		 * @inheritdoc
		 */
		public function getCallback(): callable {
			return $this->callback;
		}

		/**
		 * @inheritdoc
		 */
		public function getParameterCount(): int {
			return count($this->getParameterList());
		}

		/**
		 * @inheritdoc
		 */
		public function getParameterList(): array {
			if ($this->parameterList === null) {
				$this->parameterList = ReflectionUtils::getParameterList($this->callback);
			}
			return $this->parameterList;
		}

		/**
		 * @inheritdoc
		 */
		public function __invoke(...$parameterList) {
			return $this->invoke(...$parameterList);
		}

		/**
		 * @inheritdoc
		 */
		public function invoke(...$parameterList) {
			return call_user_func_array($this->callback, $parameterList);
		}
	}
