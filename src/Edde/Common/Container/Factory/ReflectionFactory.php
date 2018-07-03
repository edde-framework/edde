<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Callback\IParameter;
	use Edde\Api\Container\IContainer;
	use Edde\Common\Callback\CallbackUtils;
	use ReflectionClass;

	/**
	 * Simple constructor-based class cache.
	 */
	class ReflectionFactory extends AbstractFactory {
		/**
		 * @var string
		 */
		protected $class;
		/**
		 * @var IParameter[]
		 */
		protected $parameterList;

		/**
		 * @param string $name
		 * @param string $class
		 * @param bool $singleton
		 */
		public function __construct(string $name, string $class, bool $singleton = true) {
			parent::__construct($name, $singleton);
			$this->class = $class;
		}

		/**
		 * @inheritdoc
		 */
		public function getParameterList(string $name = null): array {
			if ($this->parameterList === null) {
				$this->parameterList = CallbackUtils::getParameterList($this->class);
			}
			return $this->parameterList;
		}

		/**
		 * @inheritdoc
		 */
		public function factory(string $name, array $parameterList, IContainer $container) {
			$reflectionClass = new ReflectionClass($this->class);
			if (empty($parameterList)) {
				return $reflectionClass->newInstance();
			}
			return $reflectionClass->newInstanceArgs($parameterList);
		}
	}
