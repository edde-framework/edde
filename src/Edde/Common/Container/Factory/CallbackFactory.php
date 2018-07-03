<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Callback\IParameter;
	use Edde\Api\Container\IContainer;
	use Edde\Common\Callback\CallbackUtils;

	/**
	 * Callback cache will use callable as cache method.
	 */
	class CallbackFactory extends AbstractFactory {
		/**
		 * @var callable
		 */
		protected $callback;
		/**
		 * @var IParameter[]
		 */
		protected $parameterList;

		/**
		 * The boy is smoking and leaving smoke rings into the air. The girl gets irritated with the smoke and says to her lover: "Can't you see the warning written on the cigarettes packet, smoking is injurious to health!" The boy replies back: "Darling, I am a programmer. We don't worry about warnings, we only worry about errors."
		 *
		 * @param string $name
		 * @param callable $callback
		 * @param bool $singleton
		 */
		public function __construct(string $name, callable $callback, bool $singleton = true) {
			parent::__construct($name, $singleton);
			$this->callback = $callback;
		}

		/**
		 * @inheritdoc
		 */
		public function getParameterList(string $name = null): array {
			if ($this->parameterList === null) {
				$this->parameterList = CallbackUtils::getParameterList($this->callback);
			}
			return $this->parameterList;
		}

		/**
		 * @inheritdoc
		 */
		public function factory(string $name, array $parameterList, IContainer $container) {
			return call_user_func_array($this->callback, $parameterList);
		}
	}
