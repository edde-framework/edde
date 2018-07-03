<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\IContainer;

	/**
	 * If the class exists, instance is created.
	 */
	class ClassFactory extends ReflectionFactory {
		/**
		 * What time is it?
		 *
		 * It is later than you think.
		 */
		public function __construct() {
			parent::__construct(static::class, static::class, false);
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function canHandle(string $name): bool {
			return class_exists($name);
		}

		/**
		 * @inheritdoc
		 */
		public function factory(string $name, array $parameterList, IContainer $container) {
			return parent::factory($this->class = $name, $parameterList, $container);
		}
	}
