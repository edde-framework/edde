<?php
	declare(strict_types=1);
	namespace Edde\Container\Factory;

	use Edde\Service\Application\Context;

	class CascadeFactory extends AbstractDiscoveryFactory {
		use Context;

		/**
		 * @inheritdoc
		 */
		protected function discover(string $name): array {
			return $this->context->cascade('\\', $name);
		}
	}
