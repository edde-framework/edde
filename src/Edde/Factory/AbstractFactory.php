<?php
	declare(strict_types=1);
	namespace Edde\Factory;

	use Edde\Container\IContainer;
	use Edde\Edde;

	/**
	 * Basic implementation for all dependency factories.
	 */
	abstract class AbstractFactory extends Edde implements IFactory {
		/** @inheritdoc */
		public function getUuid(): ?string {
			return null;
		}

		/** @inheritdoc */
		public function getFactory(IContainer $container): IFactory {
			return $this;
		}

		/** @inheritdoc */
		public function fetch(IContainer $container, string $name, array $params) {
		}

		/** @inheritdoc */
		public function push(IContainer $container, $instance) {
			return $instance;
		}
	}
