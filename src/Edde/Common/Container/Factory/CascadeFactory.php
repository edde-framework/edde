<?php
	declare(strict_types=1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Application\LazyContextTrait;
	use Edde\Ext\Container\AbstractDiscoveryFactory;

	class CascadeFactory extends AbstractDiscoveryFactory {
		use LazyContextTrait;

		/**
		 * @inheritdoc
		 */
		protected function discovery(string $name): array {
			return $this->context->cascade('\\', $name);
		}
	}
