<?php
	declare(strict_types=1);
	namespace Edde\Common\Container\Factory;

	use Edde\Inject\Application\Context;

	class CascadeFactory extends AbstractDiscoveryFactory {
		use Context;

		/**
		 * @inheritdoc
		 */
		protected function discover(string $name): array {
			return $this->context->cascade('\\', $name);
		}
	}
