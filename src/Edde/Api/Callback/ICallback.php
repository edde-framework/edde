<?php
	declare(strict_types = 1);

	namespace Edde\Api\Callback;

	/**
	 * Encapsulation for any callable.
	 */
	interface ICallback {
		/**
		 * @return callable
		 */
		public function getCallback(): callable;

		/**
		 * return array of dependencies (parameter list)
		 *
		 * @return IParameter[]
		 */
		public function getParameterList(): array;

		/**
		 * return total number of parameters required by this callback
		 *
		 * @return int
		 */
		public function getParameterCount(): int;

		/**
		 * @param array ...$parameterList
		 *
		 * @return mixed
		 */
		public function invoke(...$parameterList);

		/**
		 * @param array $parameterList
		 *
		 * @return mixed
		 */
		public function __invoke(...$parameterList);
	}
