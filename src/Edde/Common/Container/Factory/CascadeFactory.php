<?php
	declare(strict_types=1);
	namespace Edde\Common\Container\Factory;

	class CascadeFactory extends AbstractDiscoveryFactory {
		use Edde\Inject\Application\Context;

		/**
		 * @inheritdoc
		 */
		protected function discover(string $name): array {
			return $this->context->cascade('\\', $name);
		}
	}
