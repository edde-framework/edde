<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Common\Callback\CallbackUtils;

	/**
	 * Magical implementation of callback search mechanism based on "class exists".
	 */
	class CascadeFactory extends ClassFactory implements ILazyInject {
		use LazyContainerTrait;
		use LazyLogServiceTrait;
		/**
		 * @var callable
		 */
		protected $source;
		protected $nameList = [];

		/**
		 * "The primary purpose of the DATA statement is to give names to constants; instead of referring to pi as 3.141592653589793 at every appearance, the variable PI can be given that value with a DATA statement and used instead of the longer form of the constant. This also simplifies modifying the program, should the value of pi change."
		 *
		 * -- FORTRAN manual for Xerox Computers
		 *
		 * @param callable $source
		 */
		public function __construct(callable $source) {
			parent::__construct();
			$this->source = $source;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(string $name): bool {
			if ($discover = $this->discover($name)) {
				return parent::canHandle($discover);
			}
			return false;
		}

		/**
		 * @param string $name
		 *
		 * @return string|null
		 */
		protected function discover(string $name) {
			try {
				if (isset($this->nameList[$name]) || array_key_exists($name, $this->nameList)) {
					return $this->nameList[$name];
				}
				/** @noinspection ForeachSourceInspection */
				foreach ($this->container->call($this->source, $name) as $source) {
					if (class_exists($source)) {
						return $this->nameList[$name] = $source;
					}
				}
				return $this->nameList[$name] = null;
			} catch (\Exception $e) {
				$this->logService->exception($e, ['edde']);
				/**
				 * $this->nameList[...] is missing to try discover next time
				 */
				return null;
			}
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function getParameterList(string $name = null): array {
			return CallbackUtils::getParameterList($this->discover($name));
		}

		/**
		 * @inheritdoc
		 */
		public function factory(string $name, array $parameterList, IContainer $container) {
			return parent::factory($this->discover($name), $parameterList, $container);
		}
	}
